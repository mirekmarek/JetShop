<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_WarehouseManagement_Warehouses;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Warehouse Management - Warehouses',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_WarehouseManagement_Warehouses extends Core_Admin_Managers_WarehouseManagement_Warehouses
{

}