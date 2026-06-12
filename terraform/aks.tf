resource "azurerm_kubernetes_cluster" "main" {
  name                = "${var.project_name}-aks-${var.environment}"
  location            = data.azurerm_resource_group.main.location
  resource_group_name = data.azurerm_resource_group.main.name
  dns_prefix          = "${var.project_name}-aks-${var.environment}"

  default_node_pool {
    name            = "default"
    vm_size         = var.aks_vm_size
    os_disk_size_gb = 30
    vnet_subnet_id  = azurerm_subnet.aks.id

    enable_auto_scaling = true
    min_count           = 1
    max_count           = 5

    upgrade_settings {
      max_surge                     = "10%"
      drain_timeout_in_minutes      = 0
      node_soak_duration_in_minutes = 0
    }
  }

  identity {
    type = "SystemAssigned"
  }

  network_profile {
    network_plugin = "azure"
    service_cidr   = "10.1.0.0/16"
    dns_service_ip = "10.1.0.10"
  }
  key_vault_secrets_provider {
    secret_rotation_enabled = true
  }
  tags                = local.common_tags
  oidc_issuer_enabled = true
}

data "azurerm_client_config" "current" {}
