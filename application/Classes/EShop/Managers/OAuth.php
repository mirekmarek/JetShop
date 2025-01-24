<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_OAuth;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'OAuth',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class EShop_Managers_OAuth extends Core_EShop_Managers_OAuth
{

}