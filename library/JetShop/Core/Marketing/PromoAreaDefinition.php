<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel_Definition;

use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Application_Service_Admin_Marketing_PromoAreaDefinitions;
use JetApplication\EShopEntity_Common;
use JetApplication\EShopEntity_Definition;


#[DataModel_Definition(
	name: 'promo_area_definition',
	database_table_name: 'promo_area_definitions',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Promotion area definition',
	admin_manager_interface: Application_Service_Admin_Marketing_PromoAreaDefinitions::class
)]
abstract class Core_Marketing_PromoAreaDefinition extends EShopEntity_Common implements EShopEntity_Admin_Interface
{
	use EShopEntity_Admin_Trait;
	
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