<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_ContentEMailLayouts;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Content - e-mail layouts',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ContentEMailLayouts extends Core_Admin_Managers_ContentEMailLayouts
{
}