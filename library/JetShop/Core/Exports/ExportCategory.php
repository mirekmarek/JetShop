<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_WithEShopRelation;
use JetApplication\Exports_ExportCategory_Parameter;
use JetApplication\EShop;
use JetApplication\Exports_ExportCategory;

#[DataModel_Definition(
	name: 'exports_category',
	database_table_name: 'exports_categories',
)]
class Core_Exports_ExportCategory extends Entity_WithEShopRelation
{
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $export_code = '';
	
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
	
	public static function get( EShop $eshop, string $export_code, string $category_id ) : ?static
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['export_code'] = $export_code;
		$where[] = 'AND';
		$where['category_id'] = $category_id;
		
		return static::load( $where );
	}
	
	/**
	 * @param EShop $eshop
	 * @param string $export_code
	 * @return static[]
	 */
	public static function getCategories( EShop $eshop, string $export_code ) : array
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['export_code'] = $export_code;
		
		
		return static::fetch(
			[''=>$where],
			order_by: ['parent_category_id', 'name'],
			item_key_generator: function( Exports_ExportCategory $item ) : string {
				return $item->getCategoryId();
			}
		);
	}
	
	
	public function __construct()
	{
	}
	
	public function getExportCode(): string
	{
		return $this->export_code;
	}
	
	public function setExportCode( string $export_code ): void
	{
		$this->export_code = $export_code;
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
	 * @return Exports_ExportCategory_Parameter[]
	 */
	public function getParameters() : array
	{
		return Exports_ExportCategory_Parameter::getForCategory(
			$this->getEshop(),
			$this->export_code,
			$this->category_id
		);
	}
	
}