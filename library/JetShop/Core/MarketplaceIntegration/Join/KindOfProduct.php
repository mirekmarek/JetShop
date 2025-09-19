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
	name: 'marketplace_join_kind_of_product',
	database_table_name: 'marketplace_join_kind_of_product',
)]
abstract class Core_MarketplaceIntegration_Join_KindOfProduct extends EShopEntity_WithEShopRelation implements MarketplaceIntegration_Entity_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $kind_of_product_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	protected string $marketplace_category_id = '';
	
	
	public static function get( MarketplaceIntegration_Marketplace $marketplace, int $kind_of_product_id  ) : static|null
	{
		$i = static::load( [
			$marketplace->getWhere(),
			'AND',
			'kind_of_product_id' => $kind_of_product_id
		] );
		
		if(!$i) {
			$i = new static();
			$i->setMarketplace( $marketplace );
			$i->setKindOfProductId( $kind_of_product_id );
		}
		
		return $i;
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
