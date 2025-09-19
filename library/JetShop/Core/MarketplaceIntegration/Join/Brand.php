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
	name: 'marketplace_join_brand',
	database_table_name: 'marketplace_join_brand',
)]
abstract class Core_MarketplaceIntegration_Join_Brand extends EShopEntity_WithEShopRelation implements MarketplaceIntegration_Entity_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $brand_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $marketplace_brand_id = '';
	
	
	public static function get( MarketplaceIntegration_Marketplace $marktplace, int $brand_id  ) : static|null
	{
		$i = static::load( [
			$marktplace->getWhere(),
			'AND',
			'brand_id' => $brand_id
		] );
		
		if(!$i) {
			$i = new static();
			$i->setMarketplace( $marktplace );
			$i->setBrandId( $brand_id );
		}
		
		return $i;
	}
	
	
	public function setBrandId( int $value ) : void
	{
		$this->brand_id = $value;
	}
	
	public function getBrandId() : int
	{
		return $this->brand_id;
	}
	
	public function setMarketplaceBrandId( string $value ) : void
	{
		$this->marketplace_brand_id = $value;
	}
	
	public function getMarketplaceBrandId() : string
	{
		return $this->marketplace_brand_id;
	}
	
	public function toString(): string
	{
		return $this->marketplace_brand_id;
	}
	
	public function __toString(): string
	{
		return $this->marketplace_brand_id;
	}
}
