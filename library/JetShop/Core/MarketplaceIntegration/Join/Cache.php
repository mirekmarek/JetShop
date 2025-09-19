<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\MarketplaceIntegration_Entity_Interface;
use JetApplication\MarketplaceIntegration_Entity_Trait;
use JetApplication\MarketplaceIntegration_Marketplace;


#[DataModel_Definition(
	name: 'marketplace_join_cache',
	database_table_name: 'marketplace_join_cache',
)]
abstract class Core_MarketplaceIntegration_Join_Cache extends EShopEntity_WithEShopRelation implements MarketplaceIntegration_Entity_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	
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
	
	
	public static function get( MarketplaceIntegration_Marketplace $marketplace, string $cache_key  ) : static|null
	{
		return static::load( [
			$marketplace->getWhere(),
			'AND',
			'cache_key' => $cache_key
		] );
		
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
