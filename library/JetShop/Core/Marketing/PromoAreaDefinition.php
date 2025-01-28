<?php
namespace JetShop;

use Jet\DataModel_Definition;

use JetApplication\Entity_Admin_Interface;
use JetApplication\Entity_Admin_Trait;
use JetApplication\Admin_Managers_MarketingPromoAreaDefinitions;
use JetApplication\Entity_Common;
use JetApplication\Entity_Definition;


#[DataModel_Definition(
	name: 'promo_area_definition',
	database_table_name: 'promo_area_definitions',
)]
#[Entity_Definition(
	admin_manager_interface: Admin_Managers_MarketingPromoAreaDefinitions::class
)]
abstract class Core_Marketing_PromoAreaDefinition extends Entity_Common implements Entity_Admin_Interface
{
	use Entity_Admin_Trait;
	
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