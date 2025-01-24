<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_Banners;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Banners',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_Banners extends Core_EShop_Managers_Banners
{
	
}