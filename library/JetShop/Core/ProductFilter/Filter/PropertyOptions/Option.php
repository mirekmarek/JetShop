<?php
namespace JetShop;

use JetApplication\ProductFilter_Filter_PropertyOptions;

abstract class Core_ProductFilter_Filter_PropertyOptions_Option
{
	protected ProductFilter_Filter_PropertyOptions $filter;
	
	protected int $option_id;
	protected bool $selected = false;
	
	protected array $product_ids = [];
	
	public function __construct( ProductFilter_Filter_PropertyOptions $filter, int $option_id )
	{
		$this->filter = $filter;
		$this->option_id = $option_id;
	}
	

	public function getOptionId(): int
	{
		return $this->option_id;
	}
	
	public function getSelected(): bool
	{
		return $this->selected;
	}
	
	public function setSelected( bool $selected ): void
	{
		$this->selected = $selected;
	}
	
	public function addProductId( int $product_id ) : void
	{
		if(!in_array($product_id, $this->product_ids)) {
			$this->product_ids[] = $product_id;
		}
	}
	
	public function getProductIds(): array
	{
		return $this->product_ids;
	}
	
	
}