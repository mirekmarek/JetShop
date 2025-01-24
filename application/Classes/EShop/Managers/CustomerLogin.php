<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_CustomerLogin;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Customer Login',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_CustomerLogin extends Core_EShop_Managers_CustomerLogin
{

}