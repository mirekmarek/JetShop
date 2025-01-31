<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_Paginator;
use JetApplication\Availabilities;
use JetApplication\Availability;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\Product_Price;
use JetApplication\Product_EShopData;
use JetApplication\ProductListing_Sorter;
use JetApplication\ProductListing_Sorter_ByName;
use JetApplication\ProductListing_Sorter_Default;
use JetApplication\ProductListing_Sorter_HighestPrice;
use JetApplication\ProductListing_Sorter_LowestPrice;
use JetApplication\Property_Options_Option_EShopData;
use JetApplication\Property_EShopData;
use JetApplication\EShops;
use JetApplication\ProductFilter;
use JetApplication\ProductListing_Map;
use JetApplication\EShop;

abstract class Core_ProductListing
{
	protected EShop $eshop;
	protected Pricelist $pricelist;
	protected Availability $availability;
	
	protected ProductFilter $filter;
	protected ProductListing_Map $initial_map;
	protected ProductListing_Map $filtered_map;
	protected Data_Paginator $paginator;
	protected bool $filter_is_active;
	
	protected array $filtered_products_ids;
	protected array $visible_product_ids;
	
	protected ?int $category_id = null;
	
	/**
	 * @var Property_EShopData[]
	 */
	protected array $properties = [];
	
	/**
	 * @var Property_Options_Option_EShopData[]
	 */
	protected array $property_options = [];
	
	
	/**
	 * @var ProductListing_Sorter[]
	 */
	protected array $sorters = [];
	
	public function __construct(
		array                        $product_ids,
		?EShop                       $eshop=null,
		?Pricelist                   $pricelist =null,
		?Availability $availability=null
	)
	{
		if(!$eshop) {
			$eshop = EShops::getCurrent();
		}
		if( !$pricelist ) {
			$pricelist = Pricelists::getCurrent();
		}
		if( !$availability ) {
			$availability = Availabilities::getCurrent();
		}
		
		
		$this->eshop = $eshop;
		
		$this->pricelist = $pricelist;
		$this->availability = $availability;
		
		$this->initial_map = new ProductListing_Map($this , $product_ids );
		
		$this->filter = new ProductFilter( EShops::getCurrent() );
		$this->filter->setInitialProductIds( $this->initial_map->getProductIds() );
		
		$this->initSorters();
	}
	
	public function initSorters() : void
	{
		$default = new ProductListing_Sorter_Default($this);
		$this->sorters[$default->getKey()] = $default;
		
		$by_name = new ProductListing_Sorter_ByName($this);
		$this->sorters[$by_name->getKey()] = $by_name;
		
		$lowest_price = new ProductListing_Sorter_LowestPrice($this);
		$this->sorters[$lowest_price->getKey()] = $lowest_price;
		
		$highest_price = new ProductListing_Sorter_HighestPrice($this);
		$this->sorters[$highest_price->getKey()] = $highest_price;
		
		$default->setIsSelected( true );
		
	}
	
	/**
	 * @return ProductListing_Sorter[]
	 */
	public function getSorters(): array
	{
		return $this->sorters;
	}
	
	public function selectSorter( string $sorter_key ) : void
	{
		if(!isset($this->sorters[$sorter_key])) {
			return;
		}
		
		foreach($this->sorters as $sorter) {
			$sorter->setIsSelected( $sorter->getKey()==$sorter_key );
		}
	}
	
	public function getSelectedSorter() : ?ProductListing_Sorter
	{
		foreach($this->sorters as $sorter) {
			if( $sorter->getIsSelected() ) {
				return $sorter;
			}
		}
		
		return null;
	}
	
	

	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function getPricelist(): Pricelist
	{
		return $this->pricelist;
	}
	
	public function getAvailability(): Availability
	{
		return $this->availability;
	}
	
	
	

	public function getCategoryId(): ?int
	{
		return $this->category_id;
	}
	
	public function setCategoryId( ?int $category_id ): void
	{
		$this->category_id = $category_id;
	}
	
	
	
	public function initPaginator( int $current_page_no, int $items_per_page, callable $URL_creator ) : Data_Paginator
	{
		$this->paginator = new Data_Paginator( $current_page_no, $items_per_page, $URL_creator );
		
		return $this->paginator;
	}
	
	
	
	public function setupPriceFilter( float $price_min, float $price_max ) : void
	{
		$limit_min = $this->initial_map->getMinPrice();
		$limit_max = $this->initial_map->getMaxPrice();
		
		
		if($price_min<$limit_min) {
			$price_min = $limit_min;
		}
		if($price_max>$limit_max) {
			$price_max = $limit_max;
		}
		
		if(
			$price_min && $price_max &&
			$price_min>=$price_max
		) {
			$price_min = $limit_min;
			$price_max = $limit_max;
		}
		
		
		
		if(
			$price_min &&
			$price_min>$limit_min
		) {
			$this->filter->getPriceFilter()->setMinPrice( $price_min );
		}
		
		if(
			$price_max &&
			$price_max<$limit_max
		) {
			$this->filter->getPriceFilter()->setMaxPrice( $price_max );
		}
	}
	
	public function setProductOptionsFilter( array $rules ) : void
	{
		$valid_rules = [];
		$map = $this->initial_map->getPropertyOptionsMap();
		
		foreach($rules as $property_id=>$options) {
			$property_id = (int)$property_id;
			if(!isset($map[$property_id])) {
				continue;
			}
			
			$valid_rules[$property_id] = [];
			foreach($options as $option_id) {
				$option_id = (int)$option_id;
				if(!isset($map[$property_id][$option_id])) {
					continue;
				}
				
				$valid_rules[$property_id][] = $option_id;
			}
			
			if(!$valid_rules[$property_id]) {
				unset($valid_rules[$property_id]);
			}
		}
		
		$filter = $this->filter->getPropertyOptionsFilter();
		foreach($valid_rules as $property_id=>$selected_options) {
			$filter->setSelectedOptions( $property_id, $selected_options );
		}
	}
	
	public function setNumbersFilter( array $rules ) : void
	{
		$valid_rules = [];
		$map = $this->initial_map->getPropertyNumbersMap();
		
		foreach($rules as $property_id=>$rule) {
			$property_id = (int)$property_id;
			if(!isset($map[$property_id])) {
				continue;
			}
			
			$m_min = $this->getNumberMin( $property_id );
			$m_max = $this->getNumberMax( $property_id );
			
			if( $rule['min']!==null ) {
				$rule['min'] = (float)$rule['min'];
				
				if($rule['min']<$m_min) {
					$rule['min'] = null;
				}
			}
			
			if( $rule['max']!==null ) {
				$rule['max'] = (float)$rule['max'];
				
				if($rule['max']>$m_max) {
					$rule['max'] = null;
				}
			}
			
			
			$valid_rules[$property_id]=$rule;
		}
		
		$filter = $this->filter->getPropertyNumberFilter();
		foreach($valid_rules as $property_id=>$rule) {
			$filter->addPropertyRule( $property_id, $rule['min'], $rule['max'] );
		}
		
		
	}
	
	public function setBrandFilter( array $selected_brands ) : void
	{
		$selected_brands_validated = array_intersect(
			$this->initial_map->getBrandIds(),
			$selected_brands
		);
		
		if($selected_brands_validated) {
			$this->filter->getBrandsFilter()->setSelectedBrands( $selected_brands_validated );
		}
		
	}
	
	public function setBoolYesFilter( array $selected_yes ) : void
	{
		$_map  =$this->initial_map->getPropertyBoolMap();
		$map = [];
		foreach( $_map as $property_id=>$states ) {
			if(!empty($states[1])) {
				$map[] = $property_id;
			}
		}
		
		$selected_bools_validated = array_intersect(
			$map,
			$selected_yes
		);
		
		if($selected_bools_validated) {
			foreach($selected_bools_validated as $property_id) {
				$this->filter->getPropertyBoolFilter()->addPropertyRule( $property_id, true );
			}
		}
		
	}
	
	
	public function handle() : void
	{
		
		if($this->filter->isActive()) {
			$this->filtered_products_ids = $this->filter->filter();
			$this->filtered_products_ids = array_unique( $this->filtered_products_ids );
			$this->filtered_map = new ProductListing_Map(
				$this,
				$this->filtered_products_ids
			);
			$this->filter_is_active = true;
		} else {
			$this->filtered_products_ids = $this->filter->getInitialProductIds();
			$this->filtered_products_ids = array_unique( $this->filtered_products_ids );
			$this->filtered_map = clone $this->initial_map;
			$this->filter_is_active = false;
		}
		
		if($this->filtered_products_ids) {
			$sorter = $this->getSelectedSorter();
			
			if( $sorter ) {
				$this->filtered_products_ids = $sorter->sort( $this->filtered_products_ids );
			}
			
			$this->paginator->setData( $this->filtered_products_ids );
			
			/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
			$this->visible_product_ids = $this->paginator->getData();
		} else {
			$this->visible_product_ids = [];
		}

		
		$initial_map = $this->getInitialMap();
		$property_ids = $initial_map->getPropertyIds();
		$option_ids = $initial_map->getPropertyOptionIds();
		if($property_ids) {
			$this->properties = Property_EShopData::getActiveList( $property_ids, order_by: ['-is_default_filter', 'filter_priority', 'label'] );
			$this->property_options = Property_Options_Option_EShopData::getActiveList( $option_ids, order_by: ['priority', 'filter_label'] );
		}
		
	}
	
	public function getFilter() : ProductFilter
	{
		return $this->filter;
	}
	
	public function getInitialMap(): ProductListing_Map
	{
		return $this->initial_map;
	}
	
	public function getFilteredMap(): ProductListing_Map
	{
		return $this->filtered_map;
	}
	
	
	public function getVisibleProductIDs() : array
	{
		Product_Price::prefetch( $this->getPricelist(), $this->visible_product_ids );
		
		return $this->visible_product_ids;
	}
	
	/**
	 * @return Product_EShopData[]
	 */
	public function getVisibleProducts() : array
	{
		return Product_EShopData::getActiveList( $this->getVisibleProductIDs(), $this->eshop );
	}
	
	public function getBrandCount( int $brand_id ) : int
	{
		$map = $this->initial_map->getBrandsMap();
		if(
			!isset($map[$brand_id]) ||
			!count($map[$brand_id])
		) {
			return 0;
		}
		
		if(!$this->filter_is_active) {
			return count( $map[$brand_id] );
		}
		
		$filtered_product_ids = $this->filter->getBrandsFilter()->getPreviousFilterResult();
		if(!$filtered_product_ids) {
			return 0;
		}
		
		return count( array_intersect($filtered_product_ids, $map[$brand_id]) );
	}
	
	public function getPropertyOptionCount( int $property_id, int $option_id ) : int
	{
		$map = $this->initial_map->getPropertyOptionsMap();

		if(
			!isset($map[$property_id][$option_id]) ||
			!count($map[$property_id][$option_id])
		) {
			return 0;
		}
		
		$option_product_ids = $map[$property_id][$option_id];
		
		if(!$this->filter_is_active) {
			return count($option_product_ids);
		}
		
		$options_filter = $this->filter->getPropertyOptionsFilter();
		$brands_filter = $this->filter->getBrandsFilter();
		
		$filtered_product_ids = [
			$option_product_ids
		];
		
		
		if($options_filter->getIsActive()) {
			$options_map = $this->initial_map->getPropertyOptionsMap();
			
			$by_other_options = $options_filter->getFilteredProductsWithoutProperty( $property_id );
			
			if($by_other_options) {
				$filtered_product_ids[] = $by_other_options;
			}
		}
		
		if($brands_filter->getIsActive()) {

			$by_brand = [];
			$brand_map = $this->initial_map->getBrandsMap();
			
			foreach($brands_filter->getSelectedBrandIds() as $brand_id ) {
				$by_brand = array_merge( $by_brand, $brand_map[$brand_id]??[] );
			}
			
			$filtered_product_ids[] = $by_brand;
		}
		
		if(count($filtered_product_ids)==1) {
			$filtered_product_ids[] = $options_filter->getPreviousFilterResult();
		}
		
		$filtered_product_ids = call_user_func_array('array_intersect', $filtered_product_ids);
		
		return count( $filtered_product_ids );
	}
	
	
	public function getNumberMin( int $property_id ) : float
	{
		$map = $this->initial_map->getPropertyNumbersMap();
		return min( $map[$property_id]??[] );
	}
	
	public function getNumberMax( int $property_id ) : float
	{
		$map = $this->initial_map->getPropertyNumbersMap();
		return max( $map[$property_id]??[] );
	}
	
	public function getInitialPropertyBoolCount( int $property_id, bool $value ) : int
	{
		$map = $this->initial_map->getPropertyBoolMap();
		return count( $map[$property_id][$value?1:0]??[] );
	}
	
	public function getFilteredPropertyBoolCount( int $property_id, bool $value ) : int
	{
		$map = $this->initial_map->getPropertyBoolMap();
		return count( $map[$property_id][$value?1:0]??[] );
	}
	

	public function getPaginator(): Data_Paginator
	{
		return $this->paginator;
	}
	
	/**
	 * @return Property_EShopData[]
	 */
	public function getProperties(): array
	{
		return $this->properties;
	}
	
	/**
	 * @return Property_Options_Option_EShopData[]
	 */
	public function getPropertyOptions(): array
	{
		return $this->property_options;
	}
	
	
	
}