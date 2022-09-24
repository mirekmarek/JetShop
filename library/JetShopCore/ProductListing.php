<?php
namespace JetShop;

use Jet\MVC_View;

abstract class Core_ProductListing
{
	use ProductListing_AutoAppendProductFilter;
	
	protected static array $filter_list = [
		ProductListing_Filter_Properties::class,
		ProductListing_Filter_Brands::class,
		ProductListing_Filter_Flags::class,
		ProductListing_Filter_Price::class,
	];
	
	protected ?ProductListing_Cache $cache = null;

	protected ?Category $category = null;

	protected ?string $base_URL = null;

	protected string $base_URL_path_part = '';

	protected Shops_Shop $shop;

	protected ?array $initial_product_ids = null;

	protected ?MVC_View $filter_view = null;


	/**
	 * @var ProductListing_Filter[]
	 */
	protected array $filters = [];

	protected ?ProductListing_Sort $sort = null;

	protected ?ProductListing_Pagination $pagination = null;

	protected ?ProductListing_VariantManager $variant_manager = null;

	protected ?array $filtered_product_ids = null;

	public function __construct( Category $category, ?Shops_Shop $shop = null )
	{
		if( !$shop ) {
			$shop = Shops::getCurrent();
		}
		$this->shop = $shop;
		$this->category = $category;
		
		$this->base_URL_path_part = $category->getShopData( $this->shop )->getURLPathPart();
		$this->base_URL = $category->getShopData( $this->shop )->getURL();
		
		$this->cache = new ProductListing_Cache( $this );
		
		foreach(ProductListing::$filter_list as $class_name ) {
			/**
			 * @var ProductListing_Filter $filter
			 */
			$filter = new $class_name( $this );
			$this->filters[$filter->getKey()] = $filter;
		}
		
		$this->sort = new ProductListing_Sort( $this );
		$this->pagination = new ProductListing_Pagination( $this );
		$this->variant_manager = new ProductListing_VariantManager( $this );

	}

	public function getFilterView(): MVC_View
	{
		return $this->filter_view;
	}

	public function setFilterView( MVC_View $filter_view ): void
	{
		$this->filter_view = $filter_view;
	}


	public function prepareProductListing( array $initial_product_ids ) : void
	{
		$this->initial_product_ids = [];
		foreach( $initial_product_ids as $id ) {
			$this->initial_product_ids[] = (int)$id;
		}
		
		foreach( $this->filters as $filter ) {
			$filter->initProductListing();
		}
		
		
		$this->cache->prepareFilter( $this->initial_product_ids );
		
		foreach( $this->filters as $filter ) {
			$filter->prepareFilter( $this->initial_product_ids );
		}

		$this->variant_manager->prepareFilter( $this->initial_product_ids );
		$this->sort->prepareFilter( $this->initial_product_ids );

	}


	public function resetFilter() : void
	{
		$this->filtered_product_ids = null;
		foreach( $this->filters as $filter ) {
			$filter->resetFilter();
		}
	}

	public function resetCount() : void
	{
		$this->filtered_product_ids = null;
		foreach( $this->filters as $filter ) {
			$filter->resetCount();
		}
	}


	/**
	 * @return ProductListing_Filter[]
	 */
	public function getFilters() : array
	{
		return $this->filters;
	}

	public function filterIsActive() : bool
	{
		foreach($this->filters as $filter) {
			if($filter->filterIsActive()) {
				return true;
			}
		}

		return false;
	}



	public function properties() : ProductListing_Filter_Properties
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['properties'];
	}

	public function brands() : ProductListing_Filter_Brands
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['brands'];
	}

	public function flags() : ProductListing_Filter_Flags
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['flags'];
	}

	public function price() : ProductListing_Filter_Price
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['price'];
	}

	public function sort() : ProductListing_Sort
	{
		return $this->sort;
	}

	public function pagination() : ProductListing_Pagination
	{
		return $this->pagination;
	}

	public function cache() : ProductListing_Cache
	{
		return $this->cache;
	}

	public function getCategory() : Category
	{
		return $this->category;
	}

	public function getBaseURL() : string
	{
		return $this->base_URL;
	}

	public function setBaseURL( string $base_URL ) : void
	{
		$this->base_URL = $base_URL;
	}

	public function getBaseURLPathPart() : string
	{
		return $this->base_URL_path_part;
	}

	public function setBaseURLPathPart( string $base_URL_path_part ) : void
	{
		$this->base_URL_path_part = $base_URL_path_part;
	}

	public function getShop() : Shops_Shop
	{
		return $this->shop;
	}


	public function parseFilterUrl( array $parts ) : void
	{
		foreach($parts as $i=>$p) {
			$parts[$i] = str_replace(' ', '+', $p);
		}

		foreach( $this->filters as $filter ) {
			$filter->parseFilterUrl( $parts );
		}

		$this->sort->parseFilterUrl( $parts );
		$this->pagination->parseFilterUrl( $parts );

	}

	public function getStateData() : array
	{
		$state_data = [];

		foreach( $this->filters as $filter ) {
			$filter->getStateData( $state_data );
		}

		$this->sort->getStateData( $state_data );
		$this->pagination->getStateData( $state_data );

		return $state_data;
	}

	public function initByStateData( array $state_data ) : void
	{
		foreach( $this->filters as $filter ) {
			$filter->initByStateData( $state_data );
		}

		$this->sort->initByStateData( $state_data );
		$this->pagination->initByStateData( $state_data );
	}

	public function generateUrl( bool $with_page_no=false ) : string
	{
		$parts = [];

		foreach( $this->filters as $filter ) {
			$filter->generateUrl( $parts );
		}

		$this->sort->generateUrl( $parts );

		if($with_page_no) {
			$this->pagination->generateUrl( $parts );
		}


		if( !$parts ) {
			return $this->base_URL;
		} else {
			return $this->base_URL . '/' . implode( '/', $parts );
		}

	}

	public function getFilteredProductIds() : array
	{
		if( $this->filtered_product_ids === null ) {
			$this->filtered_product_ids = $this->initial_product_ids;

			$id_map = [];

			foreach( $this->filters as $filter ) {
				if( $filter->filterIsActive() ) {

					$ids = $filter->getFilteredProductIds();

					if( !count( $ids ) ) {
						$this->filtered_product_ids = [];
						return $this->filtered_product_ids;
					}

					$id_map[] = $ids;
				}
			}

			$this->filtered_product_ids = $this->idMapIntersect($id_map);
			
			$this->filtered_product_ids = $this->manageVariants( $this->filtered_product_ids );

			$this->filtered_product_ids = $this->sort->sort( $this->filtered_product_ids );

		}

		return $this->filtered_product_ids;
	}

	public function manageVariants( array $filtered_product_ids ) : array
	{
		return $this->variant_manager->manage( $filtered_product_ids );
	}

	public function internalGetFilteredProductIds( array $initial_product_ids, string $exclude_filter_key, callable $custom_filter_handler=null ) : array
	{
		$id_map = [];
		$id_map[] = $initial_product_ids;

		foreach( $this->filters as $filter ) {

			if( $filter->getKey()==$exclude_filter_key ) {
				if($custom_filter_handler) {
					$custom_filter_handler( $filter, $id_map );
				}

			} else {
				if( $filter->filterIsActive() ) {
					$ids = $filter->getFilteredProductIds();

					if(!$ids) {
						return [];
					}

					$id_map[] = $ids;
				}
			}
		}



		$id_map = $this->idMapIntersect($id_map);

		return $this->manageVariants( $id_map );

	}

	/**
	 * @return Product[]
	 */
	public function getVisibleProducts() : array
	{
		$ids = $this->pagination->getProductIds();

		if(!$ids) {
			return [];
		}

		$result = [];

		foreach($ids as $id) {
			$product = Product::get( $id );

			$result[ $id ] = $product;
		}

		return $result;
	}

	public function idMapIntersect( array $id_map ) : array
	{

		foreach($id_map as $ids) {
			if(!$ids) {
				return [];
			}
		}

		if(count($id_map)==0) {
			return [];
		}

		if(count($id_map)==1) {
			return $id_map[0];
		}

		return call_user_func_array('array_intersect', $id_map);
	}

	public function disableNonRelevantFilters() : void
	{
		foreach( $this->filters as $filter ) {
			$filter->disableNonRelevantFilters();
		}

		$this->resetCount();
	}
}