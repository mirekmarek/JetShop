<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\DataModel;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\Entity_WithShopData;
use JetApplication\Property;
use JetApplication\Property_Options_Option_ShopData;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'properties_options',
	database_table_name: 'properties_options',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	default_order_by: ['priority'],
	parent_model_class: Property::class
)]
abstract class Core_Property_Options_Option extends Entity_WithShopData
{
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
	 * @var Property_Options_Option_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Property_Options_Option_ShopData::class
	)]
	protected array $shop_data = [];
	
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
		foreach(Shops::getList() as $shop) {
			$this->getShopData($shop)->setPriority( $priority );
		}
	}
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	
	public function getShopData( ?Shops_Shop $shop = null ): Property_Options_Option_ShopData
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->_getShopData( $shop );
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
}