<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Marketing_LandingPages;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - Landing pages',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Marketing_LandingPages extends Core_Admin_Managers_Marketing_LandingPages
{
}