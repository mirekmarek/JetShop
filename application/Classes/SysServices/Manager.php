<?php
namespace JetApplication;

use JetShop\Core_SysServices_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'System services',
	description: '',
	module_name_prefix: ''
)]
abstract class SysServices_Manager extends Core_SysServices_Manager {
}