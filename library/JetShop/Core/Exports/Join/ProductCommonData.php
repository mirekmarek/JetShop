<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;


#[DataModel_Definition(
	name: 'exports_join_product_common_data',
	database_table_name: 'exports_join_product_common_data',
)]
abstract class Core_Exports_Join_ProductCommonData extends EShopEntity_WithEShopRelation
{
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
	)]
	protected string $export_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $common_data_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_CUSTOM_DATA
	)]
	protected mixed $common_data = null;
	
	
	public static function get( string $export_code, EShop $eshop, int $product_id, string $common_data_key  ) : static|null
	{
		return static::load( [
			'export_code' => $export_code,
			'AND',
			$eshop->getWhere(),
			'AND',
			'product_id' => $product_id,
			'AND',
			'common_data_key' => $common_data_key
		] );
		
	}
	
	
	public function setExportCode( string $value ) : void
	{
		$this->export_code = $value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}
	
	public function getExportCode() : string
	{
		return $this->export_code;
	}
	
	public function setProductId( int $value ) : void
	{
		$this->product_id = $value;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	
	public function getCommonDataKey(): string
	{
		return $this->common_data_key;
	}
	
	public function setCommonDataKey( string $common_data_key ): void
	{
		$this->common_data_key = $common_data_key;
	}
	
	public function getCommonData(): mixed
	{
		return $this->common_data;
	}
	
	public function setCommonData( mixed $common_data ): void
	{
		$this->common_data = $common_data;
	}
	
}
