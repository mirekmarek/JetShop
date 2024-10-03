<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Exports_ExportCategory_Parameter_Value;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'exports_category_param_value',
	database_table_name: 'exports_categories_params_values',
)]
class Core_Exports_ExportCategory_Parameter_Value extends Entity_WithShopRelation
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
	protected string $export_category_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $export_parameter_id = '';
	
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
	
	
	public static function get( Shops_Shop $shop, string $export_code, string $category_id, string $parameter_code, int $product_id ) : ?static
	{
		$where = $shop->getWhere();
		$where[] = 'AND';
		$where['export_code'] = $export_code;
		$where[] = 'AND';
		$where['export_category_id'] = $category_id;
		$where[] = 'AND';
		$where['export_parameter_id'] = $parameter_code;
		$where[] = 'AND';
		$where['product_id'] = $product_id;
		
		return static::load( $where );
	}
	
	/**
	 * @param Shops_Shop $shop
	 * @param string $export_code
	 * @param string $category_id
	 * @param int $product_id
	 *
	 * @return static[]
	 */
	public static function getForProduct( Shops_Shop $shop, string $export_code, string $category_id, int $product_id ) : array
	{
		$where = $shop->getWhere();
		$where[] = 'AND';
		$where['export_code'] = $export_code;
		$where[] = 'AND';
		$where['export_category_id'] = $category_id;
		$where[] = 'AND';
		$where['product_id'] = $product_id;
		
		return static::fetch(
			[''=>$where],
			item_key_generator: function( Exports_ExportCategory_Parameter_Value $item ) {
				return $item->getExportParameterId();
			});
	}
	
	
	public function getExportCode(): string
	{
		return $this->export_code;
	}
	
	public function setExportCode( string $export_code ): void
	{
		$this->export_code = $export_code;
	}
	
	public function getExportCategoryId(): string
	{
		return $this->export_category_id;
	}
	
	public function setExportCategoryId( string $export_category_id ): void
	{
		$this->export_category_id = $export_category_id;
	}
	
	public function getExportParameterId(): string
	{
		return $this->export_parameter_id;
	}
	
	public function setExportParameterId( string $export_parameter_id ): void
	{
		$this->export_parameter_id = $export_parameter_id;
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