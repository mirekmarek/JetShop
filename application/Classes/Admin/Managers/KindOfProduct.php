<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_KindOfProduct;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - Kind of products',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_KindOfProduct extends Core_Admin_Managers_KindOfProduct
{

}