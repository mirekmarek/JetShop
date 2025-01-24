<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_Wishlist;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Wishlist',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_Wishlist extends Core_EShop_Managers_Wishlist
{

}