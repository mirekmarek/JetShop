<?php
namespace JetShop;

use JetApplication\Availability;
use JetApplication\Pricelist;
use JetApplication\ProductFilter_Filter_Basic;
use JetApplication\ProductFilter_Filter_Brands;
use JetApplication\ProductFilter_Filter_Categories;
use JetApplication\ProductFilter_Filter_Price;
use JetApplication\ProductFilter_Filter_PropertyBool;
use JetApplication\ProductFilter_Filter_PropertyNumber;
use JetApplication\ProductFilter_Filter_PropertyOptions;
use JetApplication\ProductFilter_Storage;
use JetApplication\EShop;
use JetApplication\ProductFilter_Filter;


abstract class Core_ProductFilter {
	
	protected EShop $eshop;
	protected Pricelist $pricelist;
	protected Availability $availability;
	
	protected string $context_entity;
	protected int $context_entity_id;
	protected float $duration;
	
	protected ?ProductFilter_Filter_Basic $basic_filter = null;
	protected ?ProductFilter_Filter_Categories $categories_filter = null;
	protected ?ProductFilter_Filter_Price $price_filter = null;
	protected ?ProductFilter_Filter_PropertyBool $property_bool_filter = null;
	protected ?ProductFilter_Filter_PropertyNumber $property_number_filter = null;
	protected ?ProductFilter_Filter_PropertyOptions $property_options_filter = null;
	protected ?ProductFilter_Filter_Brands $brands_filter = null;
	
	protected ?array $initial_product_ids = null;
	
	/**
	 * @var ProductFilter_Filter[]
	 */
	protected array $filters = [];
	

	public function __construct(
		EShop                        $eshop,
		?Pricelist                   $pricelist =null,
		?Availability $availability=null
	)
	{
		$this->eshop = $eshop;
		
		if( !$pricelist ) {
			$pricelist = $this->eshop->getDefaultPricelist();
		}
		if( !$availability ) {
			$availability = $this->eshop->getDefaultAvailability();
		}
		
		$this->pricelist = $pricelist;
		$this->availability = $availability;
		
		
		$this->initFilters();
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
	
	
	public function initFilters() : void
	{
		$this->basic_filter = new ProductFilter_Filter_Basic();
		$this->addFilter( $this->basic_filter );
		
		$this->categories_filter = new ProductFilter_Filter_Categories();
		$this->addFilter( $this->categories_filter );
		
		$this->price_filter = new ProductFilter_Filter_Price();
		$this->addFilter( $this->price_filter );
		
		$this->property_bool_filter = new ProductFilter_Filter_PropertyBool();
		$this->addFilter( $this->property_bool_filter );
		
		$this->property_number_filter = new ProductFilter_Filter_PropertyNumber();
		$this->addFilter( $this->property_number_filter );
		
		$this->property_options_filter = new ProductFilter_Filter_PropertyOptions();
		$this->addFilter( $this->property_options_filter );
		
		$this->brands_filter = new ProductFilter_Filter_Brands();
		$this->addFilter( $this->brands_filter );
	}
	
	public function getInitialProductIds(): ?array
	{
		return $this->initial_product_ids;
	}

	public function setInitialProductIds( ?array $initial_product_ids ): void
	{
		$this->initial_product_ids = $initial_product_ids;
	}
	
	
	public function addFilter( ProductFilter_Filter $filter ) : void
	{
		$filter->setProductFilter( $this );
		$this->filters[$filter->getKey()] = $filter;
	}
	
	public function getFilter( string $key ) : ?ProductFilter_Filter
	{
		return $this->filters[$key]??null;
	}
	
	/**
	 * @return ProductFilter_Filter[]
	 */
	public function getFilters(): array
	{
		return $this->filters;
	}
	
	public function isActive() : bool
	{
		foreach($this->filters as $filter) {
			if($filter->getIsActive()) {
				return true;
			}
		}

		return false;
	}
	
	
	public function filter() : array
	{
		$start = microtime( true );
		
		$result = $this->initial_product_ids;
		
		foreach($this->filters as $filter) {
			$filter->setPreviousFilterResult( $result );
			
			if(!$filter->getIsActive()) {
				continue;
			}

			if( $result===null || $result ) {
				$filter->filter();
				$result = $filter->getFilterResult();
			}
			
		}
		
		$end = microtime( true );
		
		$this->duration = $end-$start;
		
		return $result?:[];
	}
	
	public function getContextEntity(): string
	{
		return $this->context_entity;
	}
	
	public function setContextEntity( string $context_entity ): void
	{
		$this->context_entity = $context_entity;
	}
	
	public function getContextEntityId(): int
	{
		return $this->context_entity_id;
	}
	
	public function setContextEntityId( int $context_entity_id ): void
	{
		$this->context_entity_id = $context_entity_id;
	}
	
	
	
	public function getBasicFilter(): ?ProductFilter_Filter_Basic
	{
		return $this->basic_filter;
	}
	
	public function getCategoriesFilter(): ?ProductFilter_Filter_Categories
	{
		return $this->categories_filter;
	}
	
	public function getPriceFilter(): ?ProductFilter_Filter_Price
	{
		return $this->price_filter;
	}
	
	public function getPropertyBoolFilter(): ?ProductFilter_Filter_PropertyBool
	{
		return $this->property_bool_filter;
	}
	
	public function getPropertyNumberFilter(): ?ProductFilter_Filter_PropertyNumber
	{
		return $this->property_number_filter;
	}
	
	public function getPropertyOptionsFilter(): ?ProductFilter_Filter_PropertyOptions
	{
		return $this->property_options_filter;
	}
	
	public function getBrandsFilter(): ?ProductFilter_Filter_Brands
	{
		return $this->brands_filter;
	}
	
	public function getDuration(): float
	{
		return $this->duration;
	}
	
	
	
	public function load() : void
	{
		ProductFilter_Storage::loadFilter( $this );
	}
	
	public function save() : void
	{
		ProductFilter_Storage::saveFilter( $this );
	}
	
}