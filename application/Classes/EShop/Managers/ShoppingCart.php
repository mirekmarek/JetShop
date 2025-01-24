<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_ShoppingCart;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Shopping Cart',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_ShoppingCart extends Core_EShop_Managers_ShoppingCart
{

}