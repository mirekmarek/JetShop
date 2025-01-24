<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_CustomerSection;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Customer Section',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_CustomerSection extends Core_EShop_Managers_CustomerSection
{

}