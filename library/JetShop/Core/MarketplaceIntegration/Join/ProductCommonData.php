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
	name: 'marketplace_join_product_common_data',
	database_table_name: 'marketplace_join_product_common_data',
)]
abstract class Core_MarketplaceIntegration_Join_ProductCommonData extends EShopEntity_WithEShopRelation implements MarketplaceIntegration_Entity_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	
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
	
	
	public static function get( MarketplaceIntegration_Marketplace $marketplace, int $product_id, string $common_data_key  ) : static|null
	{
		return static::load( [
			$marketplace->getWhere(),
			'AND',
			'product_id' => $product_id,
			'AND',
			'common_data_key' => $common_data_key
		] );
		
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
