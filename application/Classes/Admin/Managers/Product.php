<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Product;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - products',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Product extends Core_Admin_Managers_Product
{
}