resource "azurerm_redis_cache" "metrics" {
  name                = "${var.project_name}-redis-metrics-${var.environment}"
  location            = data.azurerm_resource_group.main.location
  resource_group_name = data.azurerm_resource_group.main.name

  capacity             = 0 # C0 = 250MB, quá đủ cho 1 app nhỏ
  family               = "C"
  sku_name             = "Standard" # giữ Standard vì production cần SLA 99.9%
  minimum_tls_version  = "1.2"
  non_ssl_port_enabled = true

  redis_configuration {
    maxmemory_policy                = "noeviction"
    maxmemory_reserved              = 30 # giảm xuống vì capacity nhỏ hơn
    maxfragmentationmemory_reserved = 30
  }

  patch_schedule {
    day_of_week    = "Sunday"
    start_hour_utc = 2
  }

  tags = local.common_tags
}
