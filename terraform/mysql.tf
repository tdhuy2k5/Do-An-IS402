resource "azurerm_mysql_flexible_server" "main" {
  name                   = "${var.project_name}-mysql-${var.environment}"
  resource_group_name    = data.azurerm_resource_group.main.name
  location               = data.azurerm_resource_group.main.location
  administrator_login    = var.mysql_admin_username
  administrator_password = var.mysql_admin_password
  sku_name               = var.mysql_sku_name
  version                = var.mysql_version


  delegated_subnet_id = azurerm_subnet.database.id
  private_dns_zone_id = azurerm_private_dns_zone.mysql.id


  depends_on = [azurerm_private_dns_zone_virtual_network_link.mysql]

  lifecycle {
    ignore_changes = [zone]
  }

  storage {
    size_gb = var.mysql_allocated_storage
  }

  tags = local.common_tags
}


resource "azurerm_mysql_flexible_database" "app" {
  name                = "esapp"
  resource_group_name = data.azurerm_resource_group.main.name
  server_name         = azurerm_mysql_flexible_server.main.name
  charset             = "utf8mb4"
  collation           = "utf8mb4_unicode_ci"
}


resource "azurerm_mysql_flexible_server_configuration" "audit_log" {
  name                = "audit_log_events"
  resource_group_name = data.azurerm_resource_group.main.name
  server_name         = azurerm_mysql_flexible_server.main.name
  value               = "CONNECTION,DDL,DML_NONSELECT,DML_SELECT"
}