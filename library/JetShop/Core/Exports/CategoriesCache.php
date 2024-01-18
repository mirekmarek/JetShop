<?php
/**
 * 
 */

namespace JetShop;

use DateInterval;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;

use Jet\DataModel_IDController_Passive;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'exports_categories_cache',
	database_table_name: 'exports_categories_cache',
	id_controller_class: DataModel_IDController_Passive::class
	
)]
abstract class Core_Exports_CategoriesCache extends Entity_WithShopRelation
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $export_code = '';


	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA,
	)]
	protected array $data = [];

	/**
	 * @var ?Data_DateTime
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $rec_created = null;
	
	
	public static function get( string $export_code, Shops_Shop $shop, callable $getter ) : array
	{
		$rec = static::load( [
			'export_code' => $export_code,
			'AND',
			$shop->getWhere()
		] );

		if(!$rec) {
			$rec = new static();
		} else {
			$deadline = Data_DateTime::now();
			$deadline->sub( new DateInterval('PT24H') );
			if( !($rec->rec_created<$deadline) ) {
				$categories = $rec->data;
				if( count($categories) ) {
					return $categories;
				}
			}
		}

		$categories = $getter();

		if(!$categories) {
			if( count($rec->data) ) {
				return $rec->data;
			}
			return [];
		}

		$rec->setShop( $shop );
		$rec->export_code = $export_code;
		$rec->rec_created = Data_DateTime::now();
		$rec->data = $categories;
		$rec->save();

		return $categories;

	}

	public static function reset( string $export_code, Shops_Shop $shop ) : void
	{
		$rec = static::load( [
			'export_code' => $export_code,
			'AND',
			$shop->getWhere()
		] );

		$rec?->delete();
	}


}
