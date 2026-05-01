param(
    [Parameter(Mandatory = $false)]
    [string]$VaultName = "kv-esapp-local",

    [Parameter(Mandatory = $false)]
    [string]$OutputFile = ".secrets.local.env",

    [Parameter(Mandatory = $false)]
    [ValidateSet("EnvFile", "GitHubEnv")]
    [string]$OutputFormat = "EnvFile"
)

$ErrorActionPreference = "Stop"

if (Get-Variable PSNativeCommandUseErrorActionPreference -ErrorAction SilentlyContinue) {
    $PSNativeCommandUseErrorActionPreference = $false
}

function Get-SecretValue {
    param(
        [string]$KeyVault,
        [string]$SecretName,
        [switch]$Optional
    )

    try {
        $value = az keyvault secret show --vault-name $KeyVault --name $SecretName --query value -o tsv --only-show-errors 2>$null
        if (-not $value -and -not $Optional) {
            throw "Secret '$SecretName' is empty in vault '$KeyVault'."
        }
        return $value
    }
    catch {
        if ($Optional) {
            return ""
        }
        throw "Unable to read secret '$SecretName' from vault '$KeyVault'. $_"
    }
}

if (-not (Get-Command az -ErrorAction SilentlyContinue)) {
    throw "Azure CLI is not installed. Install it first: https://aka.ms/installazurecliwindows"
}

$null = az account show --output none --only-show-errors 2>$null
if ($LASTEXITCODE -ne 0) {
    throw "You are not logged in. Run: az login"
}

$secrets = [ordered]@{
    APP_KEY                      = Get-SecretValue -KeyVault $VaultName -SecretName "APP-KEY"
    DB_PASSWORD                  = Get-SecretValue -KeyVault $VaultName -SecretName "DB-PASSWORD"
    MYSQL_ROOT_PASSWORD          = Get-SecretValue -KeyVault $VaultName -SecretName "MYSQL-ROOT-PASSWORD"
    JWT_SECRET                   = Get-SecretValue -KeyVault $VaultName -SecretName "JWT-SECRET"

    GOOGLE_CLIENT_ID             = Get-SecretValue -KeyVault $VaultName -SecretName "GOOGLE-CLIENT-ID" -Optional
    GOOGLE_CLIENT_SECRET         = Get-SecretValue -KeyVault $VaultName -SecretName "GOOGLE-CLIENT-SECRET" -Optional
    GOOGLE_REDIRECT_URI          = Get-SecretValue -KeyVault $VaultName -SecretName "GOOGLE-REDIRECT-URI" -Optional

    WORKLOAD_IDENTITY_CLIENT_ID  = Get-SecretValue -KeyVault $VaultName -SecretName "WORKLOAD-IDENTITY-CLIENT-ID" -Optional
    AZURE_TENANT_ID_VALUE        = Get-SecretValue -KeyVault $VaultName -SecretName "AZURE-TENANT-ID" -Optional
    INGRESS_APP_HOST             = Get-SecretValue -KeyVault $VaultName -SecretName "INGRESS-APP-HOST" -Optional
    INGRESS_API_HOST             = Get-SecretValue -KeyVault $VaultName -SecretName "INGRESS-API-HOST" -Optional
}

if (-not $secrets.GOOGLE_CLIENT_ID) {
    $secrets.GOOGLE_CLIENT_ID = "your-google-client-id.apps.googleusercontent.com"
}
if (-not $secrets.GOOGLE_REDIRECT_URI) {
    $secrets.GOOGLE_REDIRECT_URI = "http://localhost:5173/auth/callback"
}

if ($OutputFormat -eq "GitHubEnv") {
    if (-not $env:GITHUB_ENV) {
        throw "GITHUB_ENV is not set. GitHub Actions provides this automatically."
    }

    foreach ($item in $secrets.GetEnumerator()) {
        if ($item.Value) {
            Add-Content -Path $env:GITHUB_ENV -Value "$($item.Key)=$($item.Value)"
        }
    }
    Write-Host "Exported secrets to GITHUB_ENV from vault: $VaultName"
}
else {
    $lines = @()
    foreach ($item in $secrets.GetEnumerator()) {
        if ($item.Value) {
            $lines += "$($item.Key)=$($item.Value)"
        }
    }

    Set-Content -Path $OutputFile -Value $lines -Encoding UTF8
    Write-Host "Wrote secrets file: $OutputFile"
    Write-Host "Next: docker compose --env-file $OutputFile up -d --build"
}
