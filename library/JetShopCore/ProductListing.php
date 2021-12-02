<?php
namespace JetShop;

use Jet\Form;
use Jet\MVC_View;

abstract class Core_ProductListing
{
	protected ?ProductListing_Cache $cache = null;

	protected ?Category $category = null;

	protected ?string $base_URL = null;

	protected string $base_URL_path_part = '';

	protected Shops_Shop $shop;

	protected ?array $initial_product_ids = null;

	protected ?MVC_View $filter_view = null;


	/**
	 * @var ProductListing_Filter_Abstract[]
	 */
	protected array $filters = [];

	protected ?ProductListing_Sort $sort = null;

	protected ?ProductListing_Pagination $pagination = null;

	protected ?ProductListing_VariantManager $variant_manager = null;

	protected ?array $filtered_product_ids = null;

	protected ?Form $_target_filter_edit_form = null;

	public function __construct( ?Shops_Shop $shop = null )
	{
		if( !$shop ) {
			$shop = Shops::getCurrent();
		}
		$this->shop = $shop;

	}

	public function getFilterView(): MVC_View
	{
		return $this->filter_view;
	}

	public function setFilterView( MVC_View $filter_view ): void
	{
		$this->filter_view = $filter_view;
	}



	public function init() : void
	{
		$this->cache = new ProductListing_Cache( $this );
		$properties = new ProductListing_Filter_Properties( $this );
		$brands = new ProductListing_Filter_Brands( $this );
		$flags = new ProductListing_Filter_Flags( $this );
		$price = new ProductListing_Filter_Price( $this );

		$this->filters[$properties->getKey()] = $properties;
		$this->filters[$brands->getKey()] = $brands;
		$this->filters[$flags->getKey()] = $flags;
		$this->filters[$price->getKey()] = $price;

		$this->sort = new ProductListing_Sort( $this );
		$this->pagination = new ProductListing_Pagination( $this );
		$this->variant_manager = new ProductListing_VariantManager( $this );
	}


	public function prepare( array $initial_product_ids ) : void
	{
		$this->cache->prepare( $initial_product_ids );

		$this->initial_product_ids = [];
		foreach( $initial_product_ids as $id ) {
			$this->initial_product_ids[] = (int)$id;
		}

		foreach( $this->filters as $filter ) {
			$filter->prepareFilter( $this->initial_product_ids );
		}

		$this->variant_manager->prepare( $initial_product_ids );
		$this->sort->prepare( $initial_product_ids );

	}

	public function getInitialProductIds() : array
	{
		return $this->initial_product_ids;
	}

	public function resetFilter() : void
	{
		$this->filtered_product_ids = null;
		foreach( $this->filters as $filter ) {
			$filter->resetFilter();
		}

		//$this->sort->resetFilter();
		//$this->pagination->resetFilter();
	}

	public function resetCount() : void
	{
		$this->filtered_product_ids = null;
		foreach( $this->filters as $filter ) {
			$filter->resetCount();
		}
	}


	/**
	 * @return ProductListing_Filter_Abstract[]
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

	public function setCategory( Category $category ) : void
	{
		$this->category = $category;

		$this->base_URL_path_part = $category->getShopData( $this->shop )->getURLPathPart();
		$this->base_URL = $category->getShopData( $this->shop )->getURL();
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

	public function getTargetFilterEditForm( array &$target_filter ) : Form
	{
		if( !$this->_target_filter_edit_form ) {
			$this->_target_filter_edit_form = new Form( 'target_filter_edit_form', [] );

			foreach( $this->filters as $filter ) {
				$filter->getTargetFilterEditForm( $this->_target_filter_edit_form, $target_filter );
			}

			$this->sort->getTargetFilterEditForm( $this->_target_filter_edit_form, $target_filter );

			$this->_target_filter_edit_form->setDoNotTranslateTexts( true );
		}

		return $this->_target_filter_edit_form;
	}

	public function catchTargetFilterEditForm( array &$target_filter ) : bool
	{
		$form = $this->getTargetFilterEditForm( $target_filter );
		if( !$form->catchInput() || !$form->validate() ) {
			return false;
		}

		foreach( $this->filters as $filter ) {
			$filter->catchTargetFilterEditForm( $form, $target_filter );
		}

		$this->sort->catchTargetFilterEditForm( $form, $target_filter );

		return true;
	}

	public function initByTargetFilter( array $target_filter ) : void
	{
		$this->init();

		foreach( $this->filters as $filter ) {
			$filter->initByTargetFilter( $target_filter );
		}

		$this->sort->initByTargetFilter( $target_filter );
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

	public function generateCategoryTargetUrl() : string
	{
		$parts = [];

		foreach( $this->filters as $filter ) {
			$filter->generateCategoryTargetUrl( $parts );
		}

		$this->sort->generateCategoryTargetUrl( $parts );


		if( !$parts ) {
			return $this->base_URL_path_part;
		} else {
			return $this->base_URL_path_part . '/' . implode( '/', $parts );

		}
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

			if( count( $id_map ) > 0 ) {
				if( count( $id_map ) == 1 ) {
					$this->filtered_product_ids = $id_map[0];
				} else {
					$this->filtered_product_ids = call_user_func_array( 'array_intersect', $id_map );
				}
			}


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