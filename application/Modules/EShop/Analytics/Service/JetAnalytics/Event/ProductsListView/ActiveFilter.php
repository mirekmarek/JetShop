<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\ProductFilter_Filter;

#[DataModel_Definition(
	name: 'ja_event_products_list_view_active_filter',
	database_table_name: 'ja_event_products_list_view_active_filter',
	parent_model_class: Event_ProductsListView::class,
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Event_ProductsListView_ActiveFilter extends DataModel_Related_1toN implements EShopEntity_HasEShopRelation_Interface {
	
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		related_to: 'main.id'
	)]
	protected int $event_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $session_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $filter_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $property_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $value_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $value = 0;
	
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public static function createNew( Event $event, ProductFilter_Filter $filter ) : static
	{
		$item = new static();
		$item->setEshop( $event->getEshop() );
		$item->session_id = $event->getSessionId();
		$item->date_time = $event->getDateTime();
		
		$item->filter_key = $filter->getKey();
		
		return $item;
	}
	
	public function getPropertyId(): int
	{
		return $this->property_id;
	}
	
	public function setPropertyId( int $property_id ): void
	{
		$this->property_id = $property_id;
	}
	
	public function getValueKey(): string
	{
		return $this->value_key;
	}
	
	public function setValueKey( string $value_key ): void
	{
		$this->value_key = $value_key;
	}
	
	public function getValue(): int
	{
		return $this->value;
	}
	
	public function setValue( int $value ): void
	{
		$this->value = $value;
	}
	
	
}