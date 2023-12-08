<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopRelation_ShopIsID;
use JetApplication\Shops_Shop;

/**
 *
 */
#[DataModel_Definition(
	name: 'exports_join_property_option',
	database_table_name: 'exports_join_property_option',
)]
abstract class Core_Exports_Join_Property_Option extends Entity_WithShopRelation_ShopIsID
{

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $export_code = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $property_id = 0;

	/**
	 * @var int
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $option_id = 0;


	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $export_option_id = '';


	public static function get( string $export_code, Shops_Shop $shop, int $property_id, int $option_id  ) : static|null
	{
		$i = static::load( [
			'export_code' => $export_code,
			'AND',
			$shop->getWhere(),
			'AND',
			'property_id' => $property_id,
			'AND',
			'option_id' => $option_id,
		] );

		if(!$i) {
			$i = new static();
			$i->setExportCode( $export_code );
			$i->setShop( $shop );
			$i->setPropertyId( $property_id );
			$i->setOptionId( $option_id );
		}

		return $i;
	}

	/**
	 * @return iterable
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}

	/**
	 * @param string $value
	 */
	public function setExportCode( string $value ) : void
	{
		$this->export_code = $value;
	}

	/**
	 * @return string
	 */
	public function getExportCode() : string
	{
		return $this->export_code;
	}

	/**
	 * @param int $value
	 */
	public function setPropertyId( int $value ) : void
	{
		$this->property_id = $value;
	}

	/**
	 * @return int
	 */
	public function getPropertyId() : int
	{
		return $this->property_id;
	}

	/**
	 * @return int
	 */
	public function getOptionId(): int
	{
		return $this->option_id;
	}

	/**
	 * @param int $option_id
	 */
	public function setOptionId( int $option_id ): void
	{
		$this->option_id = $option_id;
	}



	/**
	 * @return string
	 */
	public function getExportOptionId(): string
	{
		return $this->export_option_id;
	}

	/**
	 * @param string $export_option_id
	 */
	public function setExportOptionId( string $export_option_id ): void
	{
		$this->export_option_id = $export_option_id;
	}

	public function toString(): string
	{
		return $this->export_option_id;
	}

	public function __toString(): string
	{
		return $this->export_option_id;
	}

}
