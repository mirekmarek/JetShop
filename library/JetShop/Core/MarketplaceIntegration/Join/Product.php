<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'marketplace_join_product',
	database_table_name: 'marketplace_join_product',
)]
abstract class Core_MarketplaceIntegration_Join_Product extends EShopEntity_WithEShopRelation
{
	

	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $marketplace_code = '';
	

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	
	public static function get( string $marketplace_code, EShop $eshop, int $product_id  ) : static|null
	{
		return static::load( [
			'marketplace_code' => $marketplace_code,
			'AND',
			$eshop->getWhere(),
			'AND',
			'product_id' => $product_id
		] );

	}
	
	public static function getProductIds( string $marketplace_code, EShop $eshop) : array
	{
		return static::dataFetchCol(
			select: ['product_id'],
			where: [
				$eshop->getWhere(),
				'AND',
				'marketplace_code' => $marketplace_code,
			],
			raw_mode: true);
	}
	
	/**
	 * @param int $product_id
	 *
	 * @return static[]
	 */
	public static function getMarketplaces( int $product_id ) : array
	{
		return static::fetch( [''=>[
			'product_id' => $product_id
		]] );
		
	}
	
	public function setMarketplaceCode( string $value ) : void
	{
		$this->marketplace_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}
	
	public function getMarketplaceCode() : string
	{
		return $this->marketplace_code;
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
