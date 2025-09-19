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
use JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter;
use JetApplication\MarketplaceIntegration_MarketplaceCategory;

#[DataModel_Definition(
	name: 'marketplace_category',
	database_table_name: 'marketplace_categories',
)]
class Core_MarketplaceIntegration_MarketplaceCategory extends EShopEntity_WithEShopRelation implements MarketplaceIntegration_Entity_Interface
{
	use MarketplaceIntegration_Entity_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $category_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $category_secondary_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $parent_category_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $path = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $full_name = '';
	
	public static function get( MarketplaceIntegration_Marketplace $marketplace, string $category_id ) : ?static
	{
		$where = $marketplace->getWhere();
		$where[] = 'AND';
		$where['category_id'] = $category_id;
	
		return static::load( $where );
	}
	
	public static function getCategories( MarketplaceIntegration_Marketplace $marketplace ) : array
	{
		$where = $marketplace->getWhere();
		
		
		return static::fetch(
			[''=>$where],
			order_by: ['parent_category_id', 'name'],
			item_key_generator: function( MarketplaceIntegration_MarketplaceCategory $item ) : string {
				return $item->getCategoryId();
			}
		);
	}
	
	
	public function getCategoryId(): string
	{
		return $this->category_id;
	}

	public function setCategoryId( string $category_id ): void
	{
		$this->category_id = $category_id;
	}

	public function getCategorySecondaryId(): string
	{
		return $this->category_secondary_id;
	}
	

	public function setCategorySecondaryId( string $category_secondary_id ): void
	{
		$this->category_secondary_id = $category_secondary_id;
	}
	
	public function getParentCategoryId(): string
	{
		return $this->parent_category_id;
	}

	public function setParentCategoryId( string $parent_category_id ): void
	{
		$this->parent_category_id = $parent_category_id;
	}

	public function getPath(): array
	{
		return explode('|', $this->path);
	}

	public function setPath( array $path ): void
	{
		$this->path = implode('|', $path);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getFullName(): array
	{
		return explode('|', $this->full_name);
	}
	
	public function setFullName( array $full_name ): void
	{
		$this->full_name = implode('|', $full_name);
	}
	
	/**
	 * @return MarketplaceIntegration_MarketplaceCategory_Parameter[]
	 */
	public function getParameters() : array
	{
		return MarketplaceIntegration_MarketplaceCategory_Parameter::getForCategory(
			$this->getMarketplace(),
			$this->category_id
		);
	}

}