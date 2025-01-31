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
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'marketplace_join_brand',
	database_table_name: 'marketplace_join_brand',
)]
abstract class Core_MarketplaceIntegration_Join_Brand extends EShopEntity_WithEShopRelation
{
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $marketplace_code = '';
	
	/**
	 * @var int
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $brand_id = 0;
	
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $marketplace_brand_id = '';
	
	
	public static function get( string $marketplace_code, EShop $eshop, int $brand_id  ) : static|null
	{
		$i = static::load( [
			'marketplace_code' => $marketplace_code,
			'AND',
			$eshop->getWhere(),
			'AND',
			'brand_id' => $brand_id
		] );
		
		if(!$i) {
			$i = new static();
			$i->setEshop( $eshop );
			$i->setMarketplaceCode( $marketplace_code );
			$i->setBrandId( $brand_id );
		}
		
		return $i;
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
