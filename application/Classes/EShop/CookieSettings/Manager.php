<?php
namespace JetApplication;

use JetShop\Core_EShop_CookieSettings_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Cookie Settings',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class EShop_CookieSettings_Manager extends Core_EShop_CookieSettings_Manager {
}