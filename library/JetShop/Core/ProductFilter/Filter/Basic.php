<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\ProductFilter_Filter;
use JetApplication\ProductFilter_Filter_Basic_SubFilter;
use JetApplication\ProductFilter_Filter_Basic_SubFilter_HasDiscount;
use JetApplication\ProductFilter_Filter_Basic_SubFilter_InStock;
use JetApplication\ProductFilter_Filter_Basic_SubFilter_IsActive;
use JetApplication\ProductFilter_Filter_Basic_SubFilter_KindOfProduct;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter_Basic extends ProductFilter_Filter
{
	/**
	 * @var ProductFilter_Filter_Basic_SubFilter[]
	 */
	protected array $sub_filters = [];
	
	public function getKey(): string
	{
		return 'basic';
	}
	
	
	public function __construct()
	{
		$this->addSubFilter( new ProductFilter_Filter_Basic_SubFilter_IsActive() );
		$this->addSubFilter( new ProductFilter_Filter_Basic_SubFilter_KindOfProduct() );
		$this->addSubFilter( new ProductFilter_Filter_Basic_SubFilter_InStock() );
		$this->addSubFilter( new ProductFilter_Filter_Basic_SubFilter_HasDiscount() );
	}
	
	
	public function addSubFilter( ProductFilter_Filter_Basic_SubFilter $sub_filter ) : void
	{
		$sub_filter->setFilter( $this );
		$this->sub_filters[$sub_filter::getKey()] = $sub_filter;
	}
	
	public function removeSubFilter( string $key ) : void
	{
		if( isset( $this->sub_filters[$key] ) ) {
			unset( $this->sub_filters[$key] );
		}
	}
	
	public function getSubFilter( string $key ): ?ProductFilter_Filter_Basic_SubFilter
	{
		return $this->sub_filters[$key] ?? null;
	}
	
	/**
	 * @return ProductFilter_Filter_Basic_SubFilter[]
	 */
	public function getSubFilters(): array
	{
		return $this->sub_filters;
	}
	
	/**
	 * @return ProductFilter_Filter_Basic_SubFilter[]
	 */
	public function getCustomerUISubFilters(): array
	{
		$res = [];
		
		foreach($this->getSubFilters() as $key=>$sub_filter) {
			if($sub_filter->isForCustomerUI()) {
				$res[$key] = $sub_filter;
			}
		}
		
		return $res;
	}
	
	
	public function getSubFilter_InStock(): null|ProductFilter_Filter_Basic_SubFilter|ProductFilter_Filter_Basic_SubFilter_HasDiscount
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_InStock::getKey() );
	}
	
	public function getSubFilter_HasDiscount(): null|ProductFilter_Filter_Basic_SubFilter|ProductFilter_Filter_Basic_SubFilter_HasDiscount
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_HasDiscount::getKey() );
	}
	
	public function getSubFilter_KindOfProductId(): null|ProductFilter_Filter_Basic_SubFilter|ProductFilter_Filter_Basic_SubFilter_KindOfProduct
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_KindOfProduct::getKey() );
	}
	
	public function getSubFilter_ItemIsActive(): null|ProductFilter_Filter_Basic_SubFilter|ProductFilter_Filter_Basic_SubFilter_IsActive
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_IsActive::getKey() );
	}
	
	
	
	public function getIsActive() : bool
	{
		foreach($this->sub_filters as $sub_filter) {
			if( $sub_filter->getIsActive() ) {
				return true;
			}
		}
		return false;
	}
	
	public function filter(): void
	{
		$result = $this->previous_filter_result;
		
		foreach( $this->sub_filters as $filter ) {
			$filter->setPreviousFilterResult( $result );
			
			if(!$filter->getIsActive()) {
				continue;
			}
			
			if( $result===null || $result ) {
				$filter->filter();
				$result = $filter->getFilterResult();
			}
		}
		
		$this->filter_result = $result;
		
	}
	
	
	public function load( ProductFilter_Storage $storage ): void
	{
		foreach( $this->sub_filters as $sub_filter ) {
			$sub_filter->load( $storage );
		}
	}
	
	public function save( ProductFilter_Storage $storage ): void
	{
		foreach( $this->sub_filters as $sub_filter ) {
			$sub_filter->save( $storage );
		}
	}
	
	
	
	public function getInStock(): ?bool
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_InStock::getKey() )?->getFilterValue();
	}
	
	public function setInStock( ?bool $in_stock ): void
	{
		$this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_InStock::getKey() )?->setFiltervalue( $in_stock );
	}
	
	public function getHasDiscount(): ?bool
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_HasDiscount::getKey() )?->getFilterValue();
	}
	
	public function setHasDiscount( ?bool $has_discount ): void
	{
		$this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_HasDiscount::getKey() )?->setFiltervalue( $has_discount );
	}
	
	public function getKindOfProductId(): ?int
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_KindOfProduct::getKey() )?->getFilterValue();
	}
	
	public function setKindOfProductId( ?int $kind_of_product_id ): void
	{
		$this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_KindOfProduct::getKey() )?->setFiltervalue( $kind_of_product_id );
	}
	
	public function getItemIsActive(): ?bool
	{
		return $this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_IsActive::getKey() )?->getFilterValue();
	}
	
	public function setItemIsActive( ?bool $item_is_active ): void
	{
		$this->getSubFilter( ProductFilter_Filter_Basic_SubFilter_IsActive::getKey() )?->setFiltervalue( $item_is_active );
	}
	
}