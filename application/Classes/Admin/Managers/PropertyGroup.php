<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_PropertyGroup;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - property groups',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_PropertyGroup extends Core_Admin_Managers_PropertyGroup
{

}