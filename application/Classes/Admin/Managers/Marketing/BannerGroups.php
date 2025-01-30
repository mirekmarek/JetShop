<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Marketing_BannerGroups;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - banner groups',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Marketing_BannerGroups extends Core_Admin_Managers_Marketing_BannerGroups
{
}