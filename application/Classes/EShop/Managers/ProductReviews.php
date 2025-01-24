<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_ProductReviews;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Product reviews',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_ProductReviews extends Core_EShop_Managers_ProductReviews
{

}