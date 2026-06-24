terraform {
  required_version = ">= 1.0"
  required_providers {
    azurerm = {
      source  = "hashicorp/azurerm"
      version = "~> 3.0"
    }
  }








}

provider "azurerm" {
  features {
    key_vault {
      purge_soft_delete_on_destroy = true
    }
  }
}


data "azurerm_resource_group" "main" {
  name = var.resource_group_name
}

locals {
  env_short = substr(var.environment, 0, 1)
  common_tags = {
    environment = var.environment
    project     = var.project_name
    created_by  = "terraform"
  }
}
