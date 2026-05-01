# End-to-End Deployment Runbook (Zero → Production + Monitoring)

This runbook explains how to deploy this project from scratch to Azure using:
- Terraform (infra)
- AKS (runtime)
- ACR (images)
- Key Vault (secrets)
- GitHub Actions (CI/CD)
- ArgoCD (GitOps)
- Log Analytics (monitoring)

---

## 1) Prerequisites

Install tools locally:
- Azure CLI (`az`)
- Terraform >= 1.0
- kubectl
- Helm
- (Optional) ArgoCD CLI

Accounts/permissions:
- Azure subscription with rights to create AKS, ACR, Key Vault, MySQL, Log Analytics.
- GitHub repo admin rights (to set Environment variables).

---

## 2) Clone repository

```bash
git clone <your-repo-url>
cd Do-An-IS402
```

---

## 3) Prepare Terraform variables

Create tfvars:

```bash
cp terraform/terraform.tfvars.example terraform/terraform.tfvars
```

Edit `terraform/terraform.tfvars` and set at least:
- `resource_group_name` (existing RG)
- `environment` (`dev` or `staging`)
- `location`
- `mysql_admin_password` (strong password)
- `aks_min_node_count`, `aks_max_node_count`

---

## 4) Login Azure and deploy infrastructure

```bash
az login
az account set --subscription "<SUBSCRIPTION_ID_OR_NAME>"

cd terraform
terraform init
terraform validate
terraform plan -out=tfplan
terraform apply tfplan
```

Get outputs:

```bash
terraform output deployment_summary
terraform output acr_login_server
terraform output aks_cluster_name
terraform output keyvault_name
terraform output mysql_fqdn
```

---

## 5) Seed required secrets into Azure Key Vault

Get Key Vault name:

```bash
KV_NAME=$(terraform -chdir=terraform output -raw keyvault_name)
```

Set required app secrets:

```bash
az keyvault secret set --vault-name "$KV_NAME" --name APP-KEY --value "<app-key>"
az keyvault secret set --vault-name "$KV_NAME" --name DB-PASSWORD --value "<mysql-admin-password>"
az keyvault secret set --vault-name "$KV_NAME" --name MYSQL-ROOT-PASSWORD --value "<mysql-root-password-or-same>"
az keyvault secret set --vault-name "$KV_NAME" --name JWT-SECRET --value "<jwt-secret>"
```

Set deployment/runtime optional secrets used by workflow rendering:

```bash
az keyvault secret set --vault-name "$KV_NAME" --name WORKLOAD-IDENTITY-CLIENT-ID --value "<managed-identity-client-id>"
az keyvault secret set --vault-name "$KV_NAME" --name AZURE-TENANT-ID --value "<tenant-id>"
az keyvault secret set --vault-name "$KV_NAME" --name INGRESS-APP-HOST --value "app.<your-domain>"
az keyvault secret set --vault-name "$KV_NAME" --name INGRESS-API-HOST --value "api.<your-domain>"
```

Optional OAuth values:

```bash
az keyvault secret set --vault-name "$KV_NAME" --name GOOGLE-CLIENT-ID --value "<google-client-id>"
az keyvault secret set --vault-name "$KV_NAME" --name GOOGLE-CLIENT-SECRET --value "<google-client-secret>"
az keyvault secret set --vault-name "$KV_NAME" --name GOOGLE-REDIRECT-URI --value "https://app.<your-domain>/auth/callback"
```

---

## 6) Configure AKS access locally

```bash
AKS_NAME=$(terraform -chdir=terraform output -raw aks_cluster_name)
RG_NAME=$(terraform -chdir=terraform output -raw aks_resource_group_name)

az aks get-credentials --resource-group "$RG_NAME" --name "$AKS_NAME" --overwrite-existing
kubectl get nodes
```

---

## 7) Install core cluster addons

### 7.1 ingress-nginx
```bash
helm repo add ingress-nginx https://kubernetes.github.io/ingress-nginx
helm repo update
helm upgrade --install ingress-nginx ingress-nginx/ingress-nginx \
  --namespace ingress-nginx --create-namespace
```

### 7.2 cert-manager
```bash
helm repo add jetstack https://charts.jetstack.io
helm repo update
helm upgrade --install cert-manager jetstack/cert-manager \
  --namespace cert-manager --create-namespace \
  --set crds.enabled=true
```

Create ClusterIssuer (example with Let's Encrypt) before production ingress.

### 7.3 Secrets Store CSI driver + Azure provider
```bash
helm repo add csi-secrets-store-provider-azure https://azure.github.io/secrets-store-csi-driver-provider-azure/charts
helm repo update
helm upgrade --install csi csi-secrets-store-provider-azure/csi-secrets-store-provider-azure \
  --namespace kube-system
```

### 7.4 Metrics Server (required for HPA)
```bash
helm repo add metrics-server https://kubernetes-sigs.github.io/metrics-server/
helm repo update
helm upgrade --install metrics-server metrics-server/metrics-server \
  --namespace kube-system
```

---

## 8) Install ArgoCD and bootstrap Application

```bash
kubectl create namespace argocd --dry-run=client -o yaml | kubectl apply -f -
kubectl apply -n argocd -f https://raw.githubusercontent.com/argoproj/argo-cd/stable/manifests/install.yaml
```

Update repo URL in `k8s/argocd/application.yaml` to your repository URL, then:

```bash
kubectl apply -f k8s/argocd/application.yaml
kubectl -n argocd get applications
```

---

## 9) Configure GitHub Environment variables (production)

In GitHub: `Settings → Environments → production → Variables`, add:
- `AZURE_CLIENT_ID`
- `AZURE_TENANT_ID`
- `AZURE_SUBSCRIPTION_ID`
- `ACR_NAME`
- `KEYVAULT_NAME`
- `AKS_RESOURCE_GROUP`
- `AKS_CLUSTER_NAME`

Also configure Azure federated credential (OIDC) for this repo/environment.

---

## 10) First deployment via GitHub Actions

1. Push to `main`.
2. Workflow builds/pushes backend + frontend images to ACR.
3. Workflow renders manifests with Key Vault values.
4. Workflow commits rendered manifests.
5. Workflow triggers ArgoCD refresh.

Monitor:

```bash
kubectl -n argocd get applications
kubectl -n esapp get pods,svc,ingress
```

---

## 11) Validate autoscaling (pods + nodes)

### Pod autoscaling (HPA)
```bash
kubectl -n esapp get hpa
kubectl -n esapp describe hpa backend-hpa
kubectl -n esapp describe hpa frontend-hpa
```

### Node autoscaling (AKS cluster autoscaler)
```bash
az aks show -g "$RG_NAME" -n "$AKS_NAME" --query "agentPoolProfiles[].{name:name,min:minCount,max:maxCount,enableAutoScaling:enableAutoScaling,count:count}" -o table
```

---

## 12) Monitoring and observability

### 12.1 Log Analytics checks
```bash
LAW_ID=$(terraform -chdir=terraform output -raw log_analytics_workspace_id)
echo "$LAW_ID"
```

In Azure Portal:
- AKS → Insights
- Log Analytics Workspace → Logs

Recommended KQL starter queries:
- Kubernetes events
- Failed pods/restarts
- Container CPU/memory trends

### 12.2 Operational checks
```bash
kubectl top nodes
kubectl -n esapp top pods
kubectl -n esapp get events --sort-by=.lastTimestamp | tail -n 50
```

---

## 13) Rotation and secret update flow

1. Rotate secret in Key Vault.
2. Restart deployments or trigger rollout to refresh runtime env if needed:

```bash
kubectl -n esapp rollout restart deploy/backend deploy/frontend
```

3. Confirm pods healthy.

---

## 14) Troubleshooting quick guide

- CI fails at Azure login: verify OIDC vars + federated credential.
- CI fails Key Vault read: verify secret names and access policy/RBAC.
- HPA shows `<unknown>` metrics: verify Metrics Server is running.
- Ingress no TLS: verify cert-manager + ClusterIssuer + DNS records.
- App cannot connect DB: confirm `DB-PASSWORD`, MySQL firewall rules, and DNS reachability.

---

## 15) Destroy environment (optional)

```bash
cd terraform
terraform destroy
```

