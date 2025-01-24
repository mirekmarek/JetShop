<?php
namespace JetApplication;

use JetShop\Core_WarehouseManagement_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: false,
	name: 'Warehouse management',
	description: '',
	module_name_prefix: ''
)]
interface WarehouseManagement_Manager extends Core_WarehouseManagement_Manager
{
}