<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_AutoOffers;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Auto Offers',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_AutoOffers extends Core_EShop_Managers_AutoOffers
{

}