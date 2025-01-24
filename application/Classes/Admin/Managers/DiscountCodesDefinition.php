<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_DiscountCodesDefinition;


#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Discount Codes definition',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_DiscountCodesDefinition extends Core_Admin_Managers_DiscountCodesDefinition
{
}