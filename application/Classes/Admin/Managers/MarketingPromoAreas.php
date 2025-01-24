<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_MarketingPromoAreas;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - Promo areas',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_MarketingPromoAreas extends Core_Admin_Managers_MarketingPromoAreas
{
}