<?php
namespace JetShop;

use JetApplication\ProductFilter_Filter_Brands;

abstract class Core_ProductFilter_Filter_Brands_Brand
{
	protected ProductFilter_Filter_Brands $filter;
	
	protected int $brand_id;
	protected bool $selected = false;
	
	protected array $product_ids = [];
	
	public function __construct( ProductFilter_Filter_Brands $filter, int $brand_id )
	{
		$this->filter = $filter;
		$this->brand_id = $brand_id;
	}
	
	
	public function getBrandId(): int
	{
		return $this->brand_id;
	}
	
	public function isSelected(): bool
	{
		return $this->selected;
	}
	
	public function setSelected( bool $selected ): void
	{
		$this->selected = $selected;
	}
	
	public function setProductIds( array $product_ids ) : void
	{
		$this->product_ids = $product_ids;
	}
	
	public function getProductIds(): array
	{
		return $this->product_ids;
	}
	
	
}