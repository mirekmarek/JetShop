<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_WarehouseManagement_Overview;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Warehouse Management - Overview',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_WarehouseManagement_Overview extends Core_Admin_Managers_WarehouseManagement_Overview
{

}