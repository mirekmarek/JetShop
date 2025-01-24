<?php
namespace JetShop;

use Jet\DataModel_Definition;

use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Admin_Managers_MarketingPromoAreaDefinitions;
use JetApplication\Entity_Common;
use JetApplication\JetShopEntity_Definition;


#[DataModel_Definition(
	name: 'promo_area_definition',
	database_table_name: 'promo_area_definitions',
)]
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_MarketingPromoAreaDefinitions::class
)]
abstract class Core_Marketing_PromoAreaDefinition extends Entity_Common implements Admin_Entity_Common_Interface
{
	use Admin_Entity_Common_Trait;
	
	public static function getActiveMap() : array
	{
		return static::dataFetchPairs(
			select: [
				'internal_code',
				'id'
			],
			where: ['is_active'=>true]
		);
	}
}