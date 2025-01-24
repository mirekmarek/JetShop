<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_MarketingBanners;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - banners',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_MarketingBanners extends Core_Admin_Managers_MarketingBanners
{
}