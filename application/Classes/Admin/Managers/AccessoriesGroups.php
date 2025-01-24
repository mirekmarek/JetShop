<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_AccessoriesGroups;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - products - accessories groups',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_AccessoriesGroups extends Core_Admin_Managers_AccessoriesGroups
{

}