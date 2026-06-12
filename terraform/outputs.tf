# Azure Container Registry

output "acr_name" {
  description = "Azure Container Registry name"
  value       = azurerm_container_registry.main.name
}

output "acr_id" {
  description = "Azure Container Registry resource ID"
  value       = azurerm_container_registry.main.id
}

output "acr_login_server" {
  description = "Azure Container Registry login server"
  value       = azurerm_container_registry.main.login_server
}

output "mysql_password" {
  value     = azurerm_mysql_flexible_server.main.administrator_password
  sensitive = true
}
output "redis_host" {
  value = azurerm_redis_cache.metrics.hostname
}

output "redis_port" {
  value = azurerm_redis_cache.metrics.ssl_port
}

output "redis_primary_key" {
  value     = azurerm_redis_cache.metrics.primary_access_key
  sensitive = true
}
output "mysql_host" {
  description = "Tên miền nội bộ (FQDN) để kết nối tới MySQL Flexible Server"
  value       = azurerm_mysql_flexible_server.main.fqdn
}
output "kv_csi_client_id" {
  value = azurerm_user_assigned_identity.kv_csi.client_id
}