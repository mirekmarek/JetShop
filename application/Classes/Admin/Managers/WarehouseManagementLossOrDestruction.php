<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_WarehouseManagementLossOrDestruction;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Warehouse Management - Loss or destruction',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_WarehouseManagementLossOrDestruction extends Core_Admin_Managers_WarehouseManagementLossOrDestruction
{
}