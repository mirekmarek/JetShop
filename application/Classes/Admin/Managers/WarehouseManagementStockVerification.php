<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_WarehouseManagementStockVerification;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Warehouse Management - Stock Verification',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_WarehouseManagementStockVerification extends Core_Admin_Managers_WarehouseManagementStockVerification
{

}