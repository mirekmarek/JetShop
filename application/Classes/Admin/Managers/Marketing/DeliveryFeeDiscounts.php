<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Marketing_DeliveryFeeDiscounts;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - Delivery Fee Discounts',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Marketing_DeliveryFeeDiscounts extends Core_Admin_Managers_Marketing_DeliveryFeeDiscounts
{
}