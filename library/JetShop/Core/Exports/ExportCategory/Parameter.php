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
use JetApplication\Exports_ExportCategory_Parameter;

#[DataModel_Definition(
	name: 'exports_category_param',
	database_table_name: 'exports_categories_params',
)]
class Core_Exports_ExportCategory_Parameter extends EShopEntity_WithEShopRelation
{
	public const PARAM_TYPE_OPTIONS = 'options';
	public const PARAM_TYPE_NUMBER = 'number';
	public const PARAM_TYPE_STRING = 'string';
	
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
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	protected string $type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $units = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected array $options = [];
	
	public static function get( EShop $eshop, string $export_code, string $category_id, string $parameter_code ) : ?static
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['export_code'] = $export_code;
		$where[] = 'AND';
		$where['export_category_id'] = $category_id;
		$where[] = 'AND';
		$where['export_parameter_id'] = $parameter_code;
		
		return static::load( $where );
	}
	
	/**
	 * @param EShop $eshop
	 * @param string $export_code
	 * @param string $category_id
	 *
	 * @return static[]
	 */
	public static function getForCategory( EShop $eshop, string $export_code, string $category_id ) : array
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where['export_code'] = $export_code;
		$where[] = 'AND';
		$where['export_category_id'] = $category_id;
		
		return static::fetch(
			[''=>$where],
			order_by: 'name',
			item_key_generator: function( Exports_ExportCategory_Parameter $item ) {
				return $item->getExportParameterId();
			});
	}
	
	public function getName(): string
	{
		return $this->name;
	}
	
	
	public function setName( string $name ): void
	{
		$this->name = $name;
	}
	
	public function getUnits(): string
	{
		return $this->units;
	}
	
	public function setUnits( string $units ): void
	{
		$this->units = $units;
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
	
	public function getType(): string
	{
		return $this->type;
	}
	
	public function setType( string $type ): void
	{
		$this->type = $type;
	}
	
	public function getOptions(): array
	{
		return $this->options;
	}
	
	public function setOptions( array $options ): void
	{
		$this->options = $options;
	}
}