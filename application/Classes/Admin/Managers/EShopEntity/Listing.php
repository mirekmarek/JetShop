<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_EShopEntity_Listing;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Entity Listing',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_EShopEntity_Listing extends Core_Admin_Managers_EShopEntity_Listing
{
}