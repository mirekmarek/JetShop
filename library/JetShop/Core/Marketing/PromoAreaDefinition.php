<?php
namespace JetShop;

use Jet\DataModel_Definition;

use JetApplication\Entity_Common;



#[DataModel_Definition(
	name: 'promo_area_definition',
	database_table_name: 'promo_area_definitions',
)]
abstract class Core_Marketing_PromoAreaDefinition extends Entity_Common
{
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