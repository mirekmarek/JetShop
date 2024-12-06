<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\MarketplaceIntegration_MarketplaceBrand;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'marketplace_brand',
	database_table_name: 'marketplace_brand',
)]
class Core_MarketplaceIntegration_MarketplaceBrand extends Entity_WithEShopRelation
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $marketplace_code = '';
	
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

	
	public static function get( EShop $eshop, string $marketplace_code, string $brand_id ) : ?static
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['marketplace_code'] = $marketplace_code;
		$where[] = 'AND';
		$where['brand_id'] = $brand_id;
		
		return static::load( $where );
	}
	
	public static function getBrands( EShop $eshop, string $marketplace_code ) : array
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['marketplace_code'] = $marketplace_code;
		
		
		return static::fetch(
			[''=>$where],
			order_by: ['name'],
			item_key_generator: function( MarketplaceIntegration_MarketplaceBrand $item ) : string {
				return $item->getBrandId();
			}
		);
	}
	
	
	public function __construct()
	{
	}
	
	public function getMarketplaceCode(): string
	{
		return $this->marketplace_code;
	}
	
	public function setMarketplaceCode( string $marketplace_code ): void
	{
		$this->marketplace_code = $marketplace_code;
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