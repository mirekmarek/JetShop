<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_PriceFormatter;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Price formatter',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_PriceFormatter extends Core_EShop_Managers_PriceFormatter
{

}