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
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\ProductFilter_Filter_Basic;
use JetApplication\ProductFilter_Filter_Brands;
use JetApplication\ProductFilter_Filter_Price;
use JetApplication\ProductFilter_Filter_PropertyBool;
use JetApplication\ProductFilter_Filter_PropertyNumber;
use JetApplication\ProductFilter_Filter_PropertyOptions;
use JetApplication\ProductListing;

#[DataModel_Definition(
	name: 'ja_event_products_list_view',
	database_table_name: 'ja_event_products_list_view',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
	
)]
class Event_ProductsListView extends DataModel
{
	use EShopEntity_HasEShopRelation_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected string $event_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $session_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true
	)]
	protected ?Data_DateTime $date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $items_count = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $pages_count = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $current_page_no = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $filtered_products_ids = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $visible_product_ids = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $selected_sorter = '';
	
	/**
	 * @var Event_ProductsListView_ActiveFilter[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Event_ProductsListView_ActiveFilter::class
	)]
	protected array $acvite_filters = [];
	
	public function getEventId(): string
	{
		return $this->event_id;
	}
	
	public function setEvent( Event $event ): void
	{
		$this->setEshop( $event->getEshop() );
		$this->event_id = $event->getId();
		$this->session_id = $event->getSessionId();
		$this->date_time = $event->getDateTime();
		
		foreach($this->acvite_filters as $filter) {
			$filter->setEvent( $event );
		}
	}
	
	
	
	public function init( ProductListing $list ) : void
	{
		$this->items_count = $list->getPaginator()->getDataItemsCount();
		$this->pages_count = $list->getPaginator()->getPagesCount();
		$this->current_page_no = $list->getPaginator()->getCurrentPageNo();
		
		$this->filtered_products_ids = implode('|', $list->getFilteredProductsIDs());
		$this->visible_product_ids = implode('|', $list->getVisibleProductIDs());
		
		$this->selected_sorter = $list->getSelectedSorter()->getKey();
		
		foreach($list->getFilter()->getFilters() as $filter) {
			if(!$filter->getIsActive()) {
				continue;
			}
			
			switch(get_class($filter)) {
				case ProductFilter_Filter_Basic::class:
					if($filter->getKindOfProductId()) {
						$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
						$f->setValueKey('kind_of_product_id');
						$f->setValue( $filter->getKindOfProductId() );
						$this->acvite_filters[] = $f;
					}
					if($filter->getInStock()!==null) {
						$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
						$f->setValueKey('in_stock');
						$f->setValue( ($filter->getInStock()?1:0) );
						$this->acvite_filters[] = $f;
					}
					if($filter->getHasDiscount()!==null) {
						$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
						$f->setValueKey('has_discount');
						$f->setValue( ($filter->getHasDiscount()?1:0) );
						$this->acvite_filters[] = $f;
					}
					
					break;
				case ProductFilter_Filter_Brands::class:
					foreach($filter->getSelectedBrandIds() as $brand_id) {
						$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
						$f->setValueKey('brand_id');
						$f->setValue( $brand_id );
						$this->acvite_filters[] = $f;
					}
					break;
				case ProductFilter_Filter_Price::class:
					if($filter->getMinPrice()!==null) {
						$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
						$f->setValueKey('min_price');
						$f->setValue( round( $filter->getMinPrice()*1000 ) );
						$this->acvite_filters[] = $f;
					}
					if($filter->getMaxPrice()!==null) {
						$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
						$f->setValueKey('max_price');
						$f->setValue( round( $filter->getMaxPrice()*1000 ) );
						$this->acvite_filters[] = $f;
					}
					break;
				case ProductFilter_Filter_PropertyBool::class:
					foreach( $filter->getPropertyRules() as $property_id=>$rule ) {
						if($rule) {
							$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
							$f->setPropertyId( $property_id );
							$f->setValueKey('selected');
							$f->setValue( 1 );
							$this->acvite_filters[] = $f;
						}
					}
					break;
				case ProductFilter_Filter_PropertyNumber::class:
					foreach( $filter->getPropertyRules() as $property_id=>$rule ) {
						if($rule['min']??null) {
							$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
							$f->setPropertyId( $property_id );
							$f->setValueKey('min');
							$f->setValue( round($rule['min']*1000) );
							$this->acvite_filters[] = $f;
						}
						if($rule['max']??null) {
							$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
							$f->setPropertyId( $property_id );
							$f->setValueKey('max');
							$f->setValue( round($rule['max']*1000) );
							$this->acvite_filters[] = $f;
						}
					}
					break;
				case ProductFilter_Filter_PropertyOptions::class:
					foreach( $filter->getSelectedOptionIds( hash_by_property: true ) as $property_id=>$option_ids ) {
						foreach($option_ids as $option_id) {
							$f = Event_ProductsListView_ActiveFilter::createNew( $filter );
							$f->setPropertyId( $property_id );
							$f->setValueKey('selected');
							$f->setValue( $option_id );
							$this->acvite_filters[] = $f;
						}
					}
					break;
			}
		}
		
		
	}
	
	
	public function showLongDetails(): string
	{
		$res = '';
		
		foreach(explode('|', $this->visible_product_ids) as $product_id) {
			$res .= Application_Service_Admin::Product()->renderItemName( $product_id ).'<br>';
		}
		
		return $res;
	}
	
	public static function getFormEvent( Event $event ) : ?static
	{
		return static::load( ['event_id'=>$event->getId()] );
	}
	
}