<?php
namespace JetApplication;

use JetShop\Core_EShop_Managers_PromoAreas;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Promo areas',
	description: '',
	module_name_prefix: 'EShop.'
)]
interface EShop_Managers_PromoAreas extends Core_EShop_Managers_PromoAreas
{

}