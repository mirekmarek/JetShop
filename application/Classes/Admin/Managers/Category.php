<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Category;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - Categories',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Category extends Core_Admin_Managers_Category
{

}