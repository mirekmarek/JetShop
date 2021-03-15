<?php
/**
 * 
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form;

/**
 *
 */
#[DataModel_Definition(
	name: 'exports_join_property',
	database_table_name: 'exports_join_property',
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_Exports_Join_Property extends DataModel
{

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		is_key: true,
		form_field_type: false
	)]
	protected string $export_code = '';

	/**
	 * @var int
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true,
		form_field_type: false
	)]
	protected int $property_id = 0;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_id: true,
		is_key: true,
		max_len: 50,
		form_field_type: false
	)]
	protected string $shop_code = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
		form_field_type: false
	)]
	protected string $export_property_id = '';

	public static function get( string $export_code, string $shop_code, int $property_id  ) : static|null
	{
		$i = static::load( [
			'export_code' => $export_code,
			'AND',
			'shop_code' => $shop_code,
			'AND',
			'property_id' => $property_id
		] );

		if(!$i) {
			$i = new static();
			$i->setExportCode( $export_code );
			$i->setShopCode( $shop_code );
			$i->setPropertyId( $property_id );
		}

		return $i;
	}

	/**
	 * @return iterable
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}

	/**
	 * @param string $value
	 */
	public function setExportCode( string $value ) : void
	{
		$this->export_code = (string)$value;
		
		if( $this->getIsSaved() ) {
			$this->setIsNew();
		}
		
	}

	/**
	 * @return string
	 */
	public function getExportCode() : string
	{
		return $this->export_code;
	}

	/**
	 * @param int $value
	 */
	public function setPropertyId( int $value ) : void
	{
		$this->property_id = $value;
	}

	/**
	 * @return int
	 */
	public function getPropertyId() : int
	{
		return $this->property_id;
	}

	/**
	 * @param string $value
	 */
	public function setShopCode( string $value ) : void
	{
		$this->shop_code = $value;
	}

	/**
	 * @return string
	 */
	public function getShopCode() : string
	{
		return $this->shop_code;
	}

	/**
	 * @param string $value
	 */
	public function setExportPropertyId( string $value ) : void
	{
		$this->export_property_id = $value;
	}

	/**
	 * @return string
	 */
	public function getExportPropertyId() : string
	{
		return $this->export_property_id;
	}


	public function toString(): string
	{
		return $this->export_property_id;
	}

	public function __toString(): string
	{
		return $this->export_property_id;
	}

}
