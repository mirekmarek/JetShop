<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_Analytics;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Analytics',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_Analytics extends Core_EShop_Managers_Analytics
{

}