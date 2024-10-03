<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Shops_Shop;


#[DataModel_Definition(
	name: 'marketplace_join_product_common_data',
	database_table_name: 'marketplace_join_product_common_data',
)]
abstract class Core_MarketplaceIntegration_Join_ProductCommonData extends Entity_WithShopRelation
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
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $common_data_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected mixed $common_data = null;
	
	
	public static function get( string $marketplace_code, Shops_Shop $shop, int $product_id, string $common_data_key  ) : static|null
	{
		return static::load( [
			'marketplace_code' => $marketplace_code,
			'AND',
			$shop->getWhere(),
			'AND',
			'product_id' => $product_id,
			'AND',
			'common_data_key' => $common_data_key
		] );
		
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
	

	public function getCommonDataKey(): string
	{
		return $this->common_data_key;
	}
	
	public function setCommonDataKey( string $common_data_key ): void
	{
		$this->common_data_key = $common_data_key;
	}
	
	public function getCommonData(): mixed
	{
		return $this->common_data;
	}
	
	public function setCommonData( mixed $common_data ): void
	{
		$this->common_data = $common_data;
	}
	
}
