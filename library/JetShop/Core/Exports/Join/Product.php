<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;


#[DataModel_Definition(
	name: 'exports_join_product',
	database_table_name: 'exports_join_product',
)]
abstract class Core_Exports_Join_Product extends Entity_WithShopRelation
{
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $export_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	
	public static function get( string $marketplace_code, Shops_Shop $shop, int $product_id  ) : static|null
	{
		return static::load( [
			'export_code' => $marketplace_code,
			'AND',
			$shop->getWhere(),
			'AND',
			'product_id' => $product_id
		] );
		
	}
	
	public static function getProductIds( string $export_code, Shops_Shop $shop) : array
	{
		return static::dataFetchCol(
			select: ['product_id'],
			where: [
				$shop->getWhere(),
				'AND',
				'export_code' => $export_code,
			],
			raw_mode: true);
	}
	
	/**
	 * @param int $product_id
	 *
	 * @return static[]
	 */
	public static function getExports( int $product_id ) : array
	{
		return static::fetch( [''=>[
			'product_id' => $product_id
		]] );
		
	}
	
	public function setExportCode( string $value ) : void
	{
		$this->export_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}
	
	public function getExportCode() : string
	{
		return $this->export_code;
	}
	
	public function setProductId( int $value ) : void
	{
		$this->product_id = $value;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
}
