<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_Articles;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Articles',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_Articles extends Core_EShop_Managers_Articles
{

}