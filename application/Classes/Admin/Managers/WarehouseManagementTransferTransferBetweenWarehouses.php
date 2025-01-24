<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_WarehouseManagementTransferBetweenWarehouses;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Warehouse Management - Transfer between warehouses',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_WarehouseManagementTransferTransferBetweenWarehouses extends Core_Admin_Managers_WarehouseManagementTransferBetweenWarehouses
{

}