<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Marketing_AutoOffers;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - automatic offers',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Marketing_AutoOffers extends Core_Admin_Managers_Marketing_AutoOffers
{
}