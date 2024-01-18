<?php
namespace JetShop;

use JetApplication\ProductFilter;
use JetApplication\ProductFilter_Storage;

abstract class Core_ProductFilter_Filter
{
	protected ProductFilter $product_filter;
	
	protected bool $is_active = false;
	
	protected ?array $previous_filter_result;
	
	abstract public function getKey() : string;
	
	protected ?array $filter_result = null;
	
	public function getProductFilter(): ProductFilter
	{
		return $this->product_filter;
	}
	
	public function setProductFilter( ProductFilter $product_filter ): void
	{
		$this->product_filter = $product_filter;
	}
	
	public function getIsActive(): bool
	{
		return $this->is_active;
	}
	
	public function setPreviousFilterResult( ?array $previous_filter_result ): void
	{
		$this->previous_filter_result = $previous_filter_result;
	}
	
	public function getPreviousFilterResult(): ?array
	{
		return $this->previous_filter_result;
	}
	
	
	abstract public function filter() : void;
	
	
	public function getFilterResult() : array
	{
		return $this->filter_result;
	}
	
	abstract public function load( ProductFilter_Storage $storage ) : void;
	
	abstract public function save( ProductFilter_Storage $storage ) : void;
}