<?php

namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;

use Jet\Http_Request;
use JetShop\Product;

class Listing extends Data_Listing
{
	const EXPORT_TYPE_XLSX = 'xlsx';
	const EXPORT_TYPE_CSV = 'csv';
	
	const EXPORT_LIMIT = 500;
	
	use Listing_Export;
	
	protected ?array $all_ids = null;
	
	/**
	 * @var Listing_Column[]
	 */
	protected array $all_columns = [];
	
	public function __construct()
	{
		parent::__construct();
		$this->initColumns();
	}
	
	protected function getList(): DataModel_Fetch_Instances
	{
		return Product::getList();
	}
	
	protected function initFilters(): void
	{
		$this->filters['search'] = new Listing_Filter_Search( $this );
		$this->filters['categories'] = new Listing_Filter_Categories( $this );
		$this->filters['product_type'] = new Listing_Filter_ProductType( $this );
		$this->filters['product_kind'] = new Listing_Filter_ProductKind( $this );
		$this->filters['is_active'] = new Listing_Filter_IsActive( $this );
	}
	
	protected function initColumns(): void
	{
		$column = new Listing_Column_Edit( $this );
		$this->all_columns[$column->getKey()] = $column;
		
		$column = new Listing_Column_ID( $this );
		$this->all_columns[$column->getKey()] = $column;
		
		$column = new Listing_Column_Name( $this );
		$this->all_columns[$column->getKey()] = $column;
		
	}
	
	public function getVisibleColumnsSchema(): array
	{
		//TODO:
		return array_keys( $this->all_columns );
	}
	
	/**
	 * @return Listing_Column[]
	 */
	public function getVisibleColumns(): array
	{
		$res = [];
		
		foreach( $this->getVisibleColumnsSchema() as $column_key ) {
			$res[$column_key] = $this->all_columns[$column_key];
		}
		
		return $res;
	}
	
	
	/**
	 * @return array
	 */
	public function getGridColumns(): array
	{
		$res = [];
		
		foreach( $this->getVisibleColumnsSchema() as $column_key ) {
			$res[$column_key] = $this->all_columns[$column_key]->getAsGridColumnDefinition();
		}
		
		return $res;
	}
	
	/**
	 *
	 */
	protected function getGrid_createColumns(): void
	{
		parent::getGrid_createColumns();
		
		foreach( $this->grid->getColumns() as $key => $grid_col ) {
			$col = $this->all_columns[$key];
			
			$col->initializer( $grid_col );
			
			$grid_col->setRenderer( function( Product $item ) use ( $col ) {
				return $col->render( $item );
			} );
		}
	}
	
	public function sort_getOrderBy(): string|array
	{
		$order_by = $this->sort_getSortBy();
		
		if( $order_by ) {
			$desc = false;
			if(
				isset( $order_by[0] ) &&
				(
					$order_by[0] == '-' ||
					$order_by[0] == '+'
				)
			) {
				$desc = $order_by[0] == '-';
				$order_by = substr( $order_by, 1 );
			}
			
			if( isset( $this->all_columns[$order_by] ) ) {
				if( $desc ) {
					return $this->all_columns[$order_by]->getOrderByDesc();
				} else {
					return $this->all_columns[$order_by]->getOrderByAsc();
				}
			}
		}
		
		return '';
	}
	
	
	/**
	 * @return DataModel_Fetch_Instances
	 */
	protected function getGrid_prepareList(): DataModel_Fetch_Instances
	{
		$list = $this->getList();
		
		$list->getQuery()->setWhere( $this->getWhere() );
		
		if( ($order_by = $this->sort_getOrderBy()) ) {
			$list->getQuery()->setOrderBy( $order_by );
		}
		
		
		return $list;
	}
	
	
	public function getFilter_search(): Listing_Filter_Search
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['search'];
	}
	
	public function getFilter_categories(): Listing_Filter_Categories
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['categories'];
	}
	
	public function getFilter_product_type(): Listing_Filter_ProductType
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['product_type'];
	}
	
	public function getFilter_product_kind(): Listing_Filter_ProductKind
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['product_kind'];
	}
	
	public function getFilter_is_active(): Listing_Filter_IsActive
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters['is_active'];
	}
	
	public function getAllIds(): array
	{
		if( $this->all_ids === null ) {
			if( $this->getWhere() ) {
				$this->all_ids = Product::getIdsList( $this->getWhere(), $this->sort_getOrderBy() );
			} else {
				$this->all_ids = [];
			}
		}
		
		return $this->all_ids;
	}
	
	public function getPrevProductEditUrl( int $current_product_id ): string
	{
		$all_ids = $this->getAllIds();
		
		$index = array_search( $current_product_id, $all_ids );
		
		if( $index ) {
			$index--;
			if( isset( $all_ids[$index] ) ) {
				return Http_Request::currentURI( ['id' => $all_ids[$index]] );
			}
		}
		
		return '';
	}
	
	public function getNextProductEditUrl( int $current_product_id ): string
	{
		$all_ids = $this->getAllIds();
		
		$index = array_search( $current_product_id, $all_ids );
		if( $index !== false ) {
			$index++;
			if( isset( $all_ids[$index] ) ) {
				return Http_Request::currentURI( ['id' => $all_ids[$index]] );
			}
		}
		
		return '';
	}
	
	public function getProductEditUrl( Product $item ): string
	{
		return Http_Request::currentURI( ['id' => $item->getId()] );
	}
}