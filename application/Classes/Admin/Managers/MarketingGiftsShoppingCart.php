<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_MarketingGiftsShoppingCart;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - Gifts for shopping cart',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_MarketingGiftsShoppingCart extends Core_Admin_Managers_MarketingGiftsShoppingCart
{
}