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
use JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter_Value;

#[DataModel_Definition(
	name: 'marketplace_category_param_value',
	database_table_name: 'marketplace_categories_params_values',
)]
class Core_MarketplaceIntegration_MarketplaceCategory_Parameter_Value extends EShopEntity_WithEShopRelation implements MarketplaceIntegration_Entity_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $marketplace_category_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $marketplace_parameter_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected string $product_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $value = '';
	
	
	public static function get( MarketplaceIntegration_Marketplace $marketplace, string $category_id, string $parameter_code, int $product_id ) : ?static
	{
		$where = $marketplace->getWhere();
		$where[] = 'AND';
		$where['marketplace_category_id'] = $category_id;
		$where[] = 'AND';
		$where['marketplace_parameter_id'] = $parameter_code;
		$where[] = 'AND';
		$where['product_id'] = $product_id;
		
		return static::load( $where );
	}
	
	/**
	 * @param MarketplaceIntegration_Marketplace $marketplace
	 * @param string $category_id
	 * @param int $product_id
	 *
	 * @return static[]
	 */
	public static function getForProduct( MarketplaceIntegration_Marketplace $marketplace, string $category_id, int $product_id ) : array
	{
		$where = $marketplace->getWhere();
		$where[] = 'AND';
		$where['marketplace_category_id'] = $category_id;
		$where[] = 'AND';
		$where['product_id'] = $product_id;
		
		return static::fetch(
			[''=>$where],
			item_key_generator: function( MarketplaceIntegration_MarketplaceCategory_Parameter_Value $item ) {
				return $item->getMarketplaceParameterId();
			});
	}

	
	public function getMarketplaceCategoryId(): string
	{
		return $this->marketplace_category_id;
	}
	
	public function setMarketplaceCategoryId( string $marketplace_category_id ): void
	{
		$this->marketplace_category_id = $marketplace_category_id;
	}
	
	public function getMarketplaceParameterId(): string
	{
		return $this->marketplace_parameter_id;
	}
	
	public function setMarketplaceParameterId( string $marketplace_parameter_id ): void
	{
		$this->marketplace_parameter_id = $marketplace_parameter_id;
	}

	public function getProductId(): string
	{
		return $this->product_id;
	}

	public function setProductId( string $product_id ): void
	{
		$this->product_id = $product_id;
	}

	public function getValue(): string
	{
		return $this->value;
	}
	
	public function setValue( string $value ): void
	{
		$this->value = $value;
	}
	
	
	
}