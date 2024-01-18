<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;

/**
 *
 */
#[DataModel_Definition(
	name: 'exports_join_kind_of_product',
	database_table_name: 'exports_join_kind_of_product',
)]
abstract class Core_Exports_Join_KindOfProduct extends Entity_WithShopRelation
{

	/**
	 * @var string
	 */ 
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
	protected int $kind_of_product_id = 0;


	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $export_category_id = '';


	public static function get( string $export_code, Shops_Shop $shop, int $kind_of_product_id  ) : static|null
	{
		$i = static::load( [
			'export_code' => $export_code,
			'AND',
			$shop->getWhere(),
			'AND',
			'kind_of_product_id' => $kind_of_product_id
		] );

		if(!$i) {
			$i = new static();
			$i->setShop( $shop );
			$i->setExportCode( $export_code );
			$i->setKindOfProductId( $kind_of_product_id );
		}

		return $i;
	}


	/**
	 * @param string $value
	 */
	public function setExportCode( string $value ) : void
	{
		$this->export_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
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
	public function setKindOfProductId( int $value ) : void
	{
		$this->kind_of_product_id = $value;
	}

	/**
	 * @return int
	 */
	public function getKindOfProductId() : int
	{
		return $this->kind_of_product_id;
	}

	/**
	 * @param string $value
	 */
	public function setExportCategoryId( string $value ) : void
	{
		$this->export_category_id = $value;
	}

	/**
	 * @return string
	 */
	public function getExportCategoryId() : string
	{
		return $this->export_category_id;
	}

	public function toString(): string
	{
		return $this->export_category_id;
	}

	public function __toString(): string
	{
		return $this->export_category_id;
	}
}
