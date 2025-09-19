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
use JetApplication\MarketplaceIntegration_Marketplace;
use JetApplication\MarketplaceIntegration_MarketplaceBrand;
use JetApplication\MarketplaceIntegration_Entity_Trait;

#[DataModel_Definition(
	name: 'marketplace_brand',
	database_table_name: 'marketplace_brand',
)]
class Core_MarketplaceIntegration_MarketplaceBrand extends EShopEntity_WithEShopRelation implements MarketplaceIntegration_Entity_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $brand_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $name = '';

	
	public static function get( MarketplaceIntegration_Marketplace $marketplace, string $brand_id ) : ?static
	{
		$where = $marketplace->getWhere();
		$where[] = 'AND';
		$where['brand_id'] = $brand_id;
		
		return static::load( $where );
	}
	
	public static function getBrands( MarketplaceIntegration_Marketplace $marketplace ) : array
	{
		$where = $marketplace->getWhere();
		
		return static::fetch(
			[''=>$where],
			order_by: ['name'],
			item_key_generator: function( MarketplaceIntegration_MarketplaceBrand $item ) : string {
				return $item->getBrandId();
			}
		);
	}
	
	public function getBrandId(): string
	{
		return $this->brand_id;
	}
	
	public function setBrandId( string $brand_id ): void
	{
		$this->brand_id = $brand_id;
	}
	
	
	public function getName(): string
	{
		return $this->name;
	}
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
}