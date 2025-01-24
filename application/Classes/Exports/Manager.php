<?php
namespace JetApplication;

use JetShop\Core_Exports_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: false,
	name: 'Data exports',
	description: '',
	module_name_prefix: 'Exports.'
)]
abstract class Exports_Manager extends Core_Exports_Manager {
}