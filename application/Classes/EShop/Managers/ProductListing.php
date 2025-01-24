<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_ProductListing;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: true,
	name: 'Product Listing',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_ProductListing extends Core_EShop_Managers_ProductListing
{

}