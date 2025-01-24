<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_PriceFormatter;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Price Formatter',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_PriceFormatter extends Core_Admin_Managers_PriceFormatter
{

}