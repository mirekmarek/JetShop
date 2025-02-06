<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel_Definition;
use Jet\DataModel;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\EShopEntity_Admin_WithEShopData_Interface;
use JetApplication\EShopEntity_Admin_WithEShopData_Trait;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\EShopEntity_WithEShopData_HasImages_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\Property;
use JetApplication\Property_Options_Option_EShopData;
use JetApplication\EShops;
use JetApplication\EShop;

#[DataModel_Definition(
	name: 'properties_options',
	database_table_name: 'properties_options',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	default_order_by: ['priority'],
	parent_model_class: Property::class
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Property option',
	description_mode: true,
	separate_tab_form_shop_data: true,
	images: [
		'main' => 'Main image',
		'pictogram' => 'Pictogram image',
	]
)]
abstract class Core_Property_Options_Option extends EShopEntity_WithEShopData implements
	EShopEntity_HasImages_Interface,
	EShopEntity_Admin_WithEShopData_Interface
{
	use EShopEntity_WithEShopData_HasImages_Trait;
	use EShopEntity_Admin_WithEShopData_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $property_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	
	
	/**
	 * @var Property_Options_Option_EShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Property_Options_Option_EShopData::class
	)]
	protected array $eshop_data = [];
	
	protected bool $is_first = false;
	
	protected bool $is_last = false;
	
	public function setPropertyId( int $property_id ): void
	{
		$this->property_id = $property_id;
	}
	
	public function getPropertyId(): int
	{
		return $this->property_id;
	}
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}

	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
		foreach( EShops::getList() as $eshop) {
			$this->getEshopData($eshop)->setPriority( $priority );
		}
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	
	public function getEshopData( ?EShop $eshop = null ): Property_Options_Option_EShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getEshopData( $eshop );
	}
	
	
	public function isFirst(): bool
	{
		return $this->is_first;
	}
	
	public function setIsFirst( bool $is_first ): void
	{
		$this->is_first = $is_first;
	}
	
	public function isLast(): bool
	{
		return $this->is_last;
	}
	
	public function setIsLast( bool $is_last ): void
	{
		$this->is_last = $is_last;
	}
	
	
	public static function getListForProperty( int $property_id ) : array
	{
		$options = static::fetchInstances(['property_id'=>$property_id] );
		$options->getQuery()->setOrderBy(['priority']);
		
		$res = [];
		
		foreach($options as $opt) {
			$res[$opt->getId()] = $opt;
		}
		
		return $res;
	}
	
	public function getEditUrl( array $get_params=[] ): string
	{
		return '';
	}
}