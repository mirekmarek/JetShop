<?php

namespace JetApplicationModule\Admin\Catalog\Products;

use Jet\DataListing;
use Jet\DataModel_Fetch_Instances;

use Jet\Http_Request;
use Jet\MVC_View;
use JetApplication\Shops;

class Listing extends DataListing
{
	
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
	
	
	protected ?array $all_ids = null;
	
	protected MVC_View $column_view;
	protected MVC_View $filter_view;
	
	
	public function __construct( MVC_View $column_view, MVC_View $filter_view )
	{
		$this->column_view = $column_view;
		$this->filter_view = $filter_view;
		
		foreach(static::$all_columns_classes as $class) {
			$this->addColumn( new $class() );
		}
		
		foreach(static::$all_filters_classes as $class) {
			$this->addFilter( new $class() );
		}
		
		foreach(static::$all_exports_classes as $class) {
			$this->addExport( new $class() );
		}
		
		foreach(static::$all_operations_classes as $class) {
			$this->addOperation( new $class() );
		}
		
		$this->setDefaultSort('+name');
		
		Listing_Schema::setListing( $this );
		
		$schema = Listing_Schema::getCurrentColSchema();
		
		$index = 0;
		foreach($this->columns as $col) {
			
			if(
				$col->isMandatory() ||
				in_array($col->getKey(), $schema)
			) {
				$col->setIndex( $index );
				$col->setIsVisible( true );
				$index++;
			} else {
				$col->setIsVisible(false);
			}
		}
	}
	
	public function getItemList(): DataModel_Fetch_Instances
	{
		return Product::getList();
	}
	
	
	public function getDefaultFilterWhere(): array
	{
		return [
			[
				'products_shop_data.shop_code' => Shops::getCurrent()->getShopCode(),
				'AND',
				'products_shop_data.locale' => Shops::getCurrent()->getLocale()
			]
		];
	}
	
	
	
	protected function getIdList(): array
	{
		if( $this->all_ids === null ) {
			if( $this->getFilterWhere() ) {
				$this->all_ids = Product::dataFetchCol(select:['id'], where: $this->getFilterWhere(), order_by: $this->getQueryOrderBy() );
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
	
	
	
	public function getFilterView(): MVC_View
	{
		return $this->filter_view;
	}
	
	public function getColumnView(): MVC_View
	{
		return $this->column_view;
	}
	
	public function itemGetter( int|string $id ): ?Product
	{
		return Product::get( $id );
	}
}