<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_ContentInfoPages;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Content - Info pages',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ContentInfoPages extends Core_Admin_Managers_ContentInfoPages
{
}