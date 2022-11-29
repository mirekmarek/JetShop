<?php

namespace JetShopModule\Admin\Catalog\Products;

use Jet\Data_Listing;
use Jet\DataModel_Fetch_Instances;

use Jet\Http_Request;
use Jet\Tr;
use JetShop\Product;
use JetShop\Shops;

class Listing extends Data_Listing
{
	
	protected string $default_sort = 'name';
	
	
	protected static array $all_filters_classes = [
		Listing_Filter_Search::class,
		Listing_Filter_Categories::class,
		Listing_Filter_ProductType::class,
		Listing_Filter_ProductKind::class,
		Listing_Filter_IsActive::class
	];
	
	protected static array $all_columns_classes = [
		Listing_Column_Edit::class,
		Listing_Column_ID::class,
		Listing_Column_Name::class,
		Listing_Column_FinalPrice::class,
	];
	
	protected static array $all_exports_classes = [
		Listing_Export_CSV::class,
		Listing_Export_XLSX::class
	];
	
	protected static array $all_operations_classes = [
		Listing_Operation_Categorize::class,
		Listing_Operation_Uncategorize::class,
	];
	
	/**
	 * @var Listing_Column[]
	 */
	protected array $all_columns = [];
	
	/**
	 * @var Listing_Export[]
	 */
	protected array $exports = [];
	
	/**
	 * @var Listing_Operation[]
	 */
	protected array $operations = [];
	
	protected ?array $all_ids = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->initColumns();
		$this->initExports();
		$this->initOperations();
	}
	
	protected function getList(): DataModel_Fetch_Instances
	{
		return Product::getList();
	}
	
	protected function initFilters(): void
	{
		foreach(static::$all_filters_classes as $class) {
			$filter = new $class( $this );
			$this->filters[$filter->getKey()] = $filter;
		}
	}
	
	protected function initColumns(): void
	{
		foreach(static::$all_columns_classes as $class) {
			$column = new $class( $this );
			$this->all_columns[$column->getKey()] = $column;
		}
	}
	
	protected function initExports(): void
	{
		foreach(static::$all_exports_classes as $class) {
			$column = new $class( $this );
			$this->exports[$column->getKey()] = $column;
		}
	}
	
	protected function initOperations(): void
	{
		foreach(static::$all_operations_classes as $class) {
			$column = new $class( $this );
			$this->operations[$column->getKey()] = $column;
		}
	}
	
	/**
	 * @return Listing_Column[]
	 */
	public function getAllColumns() : array
	{
		return $this->all_columns;
	}
	
	public function getVisibleColumnsSchema(): array
	{
		$mandatory = [];
		foreach($this->getAllColumns() as $col ) {
			if($col->isMandatory()) {
				$mandatory[] = $col->getKey();
			}
		}
		
		return array_merge($mandatory, Listing_Schema::getCurrentColSchema());
	}
	
	/**
	 * @return Listing_Column[]
	 */
	public function getNotVisibleColumns(): array
	{
		$res = [];
		
		$visible = $this->getVisibleColumnsSchema();
		
		foreach( $this->all_columns as $column ) {
			if(in_array($column->getKey(), $visible)) {
				continue;
			}
			
			$res[$column->getKey()] = $column;
		}
		
		return $res;
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
	 * @return array
	 */
	public function getWhere(): array
	{
		if( $this->filter_where === null ) {
			
			$this->filter_where = [
				[
					'products_shop_data.shop_code' => Shops::getCurrent()->getShopCode(),
					'AND',
					'products_shop_data.locale' => Shops::getCurrent()->getLocale()
				]
			];
			
			foreach( $this->filters as $filter ) {
				$filter->generateWhere();
			}
			
		}
		return $this->filter_where;
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
	
	public function getFilter( string $key ) : Listing_Filter
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->filters[$key];
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
	
	/**
	 * @return Listing_Export[]
	 */
	public function getExports() : array
	{
		return $this->exports;
	}
	
	public function export( string $type ) : void
	{
		$this->exports[$type]->export();
	}
	
	public function getExportTypes() : array
	{
		$res = [];
		
		foreach($this->exports as $export) {
			$res[$export->getKey()] = Tr::_($export->getTitle());
		}
		
		return $res;
	}
	
	/**
	 * @return Listing_Operation[]
	 */
	public function getOperations() : array
	{
		return $this->operations;
	}
	
	public function operationExists( string $operation ) : bool
	{
		return isset( $this->operations[$operation] );
	}
	
	public function operation( string $operation ) : Listing_Operation
	{
		return $this->operations[$operation];
	}
}