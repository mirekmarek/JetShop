<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'marketplace_join_kind_of_product',
	database_table_name: 'marketplace_join_kind_of_product',
)]
abstract class Core_MarketplaceIntegration_Join_KindOfProduct extends EShopEntity_WithEShopRelation
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
	protected int $kind_of_product_id = 0;
	
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $marketplace_category_id = '';
	
	
	public static function get( string $marketplace_code, EShop $eshop, int $kind_of_product_id  ) : static|null
	{
		$i = static::load( [
			'marketplace_code' => $marketplace_code,
			'AND',
			$eshop->getWhere(),
			'AND',
			'kind_of_product_id' => $kind_of_product_id
		] );
		
		if(!$i) {
			$i = new static();
			$i->setEshop( $eshop );
			$i->setMarketplaceCode( $marketplace_code );
			$i->setKindOfProductId( $kind_of_product_id );
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

	public function setKindOfProductId( int $value ) : void
	{
		$this->kind_of_product_id = $value;
	}

	public function getKindOfProductId() : int
	{
		return $this->kind_of_product_id;
	}

	public function setMarketplaceCategoryId( string $value ) : void
	{
		$this->marketplace_category_id = $value;
	}

	public function getMarketplaceCategoryId() : string
	{
		return $this->marketplace_category_id;
	}
	
	public function toString(): string
	{
		return $this->marketplace_category_id;
	}
	
	public function __toString(): string
	{
		return $this->marketplace_category_id;
	}
}
