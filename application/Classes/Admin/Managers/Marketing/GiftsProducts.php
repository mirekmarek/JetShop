<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Marketing_GiftsProducts;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - Gifts for products',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Marketing_GiftsProducts extends Core_Admin_Managers_Marketing_GiftsProducts
{
}