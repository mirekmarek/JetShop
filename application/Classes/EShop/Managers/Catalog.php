<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_Catalog;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Catalog',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_Catalog extends Core_EShop_Managers_Catalog
{

}