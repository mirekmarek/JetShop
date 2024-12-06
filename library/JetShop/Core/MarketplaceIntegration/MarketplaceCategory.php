<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter;
use JetApplication\EShop;
use JetApplication\MarketplaceIntegration_MarketplaceCategory;

#[DataModel_Definition(
	name: 'marketplace_category',
	database_table_name: 'marketplace_categories',
)]
class Core_MarketplaceIntegration_MarketplaceCategory extends Entity_WithEShopRelation
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
	
	public static function get( EShop $eshop, string $marketplace_code, string $category_id ) : ?static
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['marketplace_code'] = $marketplace_code;
		$where[] = 'AND';
		$where['category_id'] = $category_id;
	
		return static::load( $where );
	}
	
	public static function getCategories( EShop $eshop, string $marketplace_code ) : array
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['marketplace_code'] = $marketplace_code;
		
		
		return static::fetch(
			[''=>$where],
			order_by: ['parent_category_id', 'name'],
			item_key_generator: function( MarketplaceIntegration_MarketplaceCategory $item ) : string {
				return $item->getCategoryId();
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
			$this->getEshop(),
			$this->marketplace_code,
			$this->category_id
		);
	}

}