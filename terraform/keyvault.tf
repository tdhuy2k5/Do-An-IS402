

resource "azurerm_key_vault" "main" {
  name                        = "${var.project_name}-kv-${var.environment}"
  location                    = data.azurerm_resource_group.main.location
  resource_group_name         = data.azurerm_resource_group.main.name
  enabled_for_disk_encryption = false
  tenant_id                   = data.azurerm_client_config.current.tenant_id
  sku_name                    = var.keyvault_sku
  tags                        = local.common_tags
}
resource "azurerm_key_vault_access_policy" "csi" {
  key_vault_id = azurerm_key_vault.main.id
  tenant_id    = data.azurerm_client_config.current.tenant_id

  object_id = azurerm_user_assigned_identity.kv_csi.principal_id

  secret_permissions = ["Get", "List"]
}
# ✅ Cấp quyền cho identity đang chạy Terraform
resource "azurerm_key_vault_access_policy" "terraform" {
  key_vault_id = azurerm_key_vault.main.id
  tenant_id    = data.azurerm_client_config.current.tenant_id
  object_id    = data.azurerm_client_config.current.object_id

  secret_permissions = ["Get", "Set", "Delete", "List", "Purge", "Recover"]
}

# ✅ Tất cả secrets depends_on access policy
resource "azurerm_key_vault_secret" "jwt_secret" {
  name         = "JWT-SECRET"
  value        = var.jwt_secret
  key_vault_id = azurerm_key_vault.main.id
  depends_on   = [azurerm_key_vault_access_policy.terraform]
}

resource "azurerm_key_vault_secret" "app_key" {
  name         = "APP-KEY"
  value        = var.app_key
  key_vault_id = azurerm_key_vault.main.id
  depends_on   = [azurerm_key_vault_access_policy.terraform]
}

resource "azurerm_key_vault_secret" "google_client_secret" {
  name         = "GOOGLE-CLIENT-SECRET"
  value        = var.google_client_secret
  key_vault_id = azurerm_key_vault.main.id
  depends_on   = [azurerm_key_vault_access_policy.terraform]
}

resource "azurerm_key_vault_secret" "mysql_username" {
  name         = "MYSQL-ADMIN-USERNAME"
  value        = var.mysql_admin_username
  key_vault_id = azurerm_key_vault.main.id
  depends_on   = [azurerm_key_vault_access_policy.terraform]
}

resource "azurerm_key_vault_secret" "mysql_password" {
  name         = "MYSQL-ROOT-PASSWORD"
  value        = var.mysql_admin_password
  key_vault_id = azurerm_key_vault.main.id
  depends_on   = [azurerm_key_vault_access_policy.terraform]
}

resource "azurerm_key_vault_secret" "redis_password" {
  name = "REDIS-PASSWORD"

  value = azurerm_redis_cache.metrics.primary_access_key

  key_vault_id = azurerm_key_vault.main.id

  depends_on = [
    azurerm_key_vault_access_policy.terraform
  ]
}
