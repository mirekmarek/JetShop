<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Marketing_ProductStickers;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - Product stickers',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Marketing_ProductStickers extends Core_Admin_Managers_Marketing_ProductStickers
{
}