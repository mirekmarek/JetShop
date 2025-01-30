<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'marketplace_join_cache',
	database_table_name: 'marketplace_join_cache',
)]
abstract class Core_MarketplaceIntegration_Join_Cache extends EShopEntity_WithEShopRelation
{
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $marketplace_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $cache_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected mixed $cache_data = null;
	
	
	public static function get( string $marketplace_code, EShop $eshop, string $cache_key  ) : static|null
	{
		return static::load( [
			'marketplace_code' => $marketplace_code,
			'AND',
			$eshop->getWhere(),
			'AND',
			'cache_key' => $cache_key
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
	
	public function getCacheKey(): string
	{
		return $this->cache_key;
	}
	
	public function setCacheKey( string $cache_key ): void
	{
		$this->cache_key = $cache_key;
	}
	
	public function getCacheData(): mixed
	{
		return $this->cache_data;
	}
	
	public function setCacheData( mixed $cache_data ): void
	{
		$this->cache_data = $cache_data;
	}
	
}
