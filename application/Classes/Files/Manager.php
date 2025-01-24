<?php
namespace JetApplication;

use JetShop\Core_Files_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: true,
	name: 'Files',
	description: '',
	module_name_prefix: ''
)]
interface Files_Manager extends Core_Files_Manager {

}