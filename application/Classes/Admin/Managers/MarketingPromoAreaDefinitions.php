<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_MarketingPromoAreaDefinitions;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Marketing - Promo area definitions',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_MarketingPromoAreaDefinitions extends Core_Admin_Managers_MarketingPromoAreaDefinitions
{
}