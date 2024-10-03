<?php
namespace JetShop;

use JetApplication\Product_ShopData;

abstract class Core_Order_ProductOverviewItem
{
	protected int $product_id = 0;
	protected Product_ShopData $product;
	protected float $number_of_units;
	
	public function __construct( Product_ShopData $product, float $number_of_units )
	{
		$this->product = $product;
		$this->product_id = $product->getId();
		$this->number_of_units = $number_of_units;
	}
	
	public function getProduct(): Product_ShopData
	{
		return $this->product;
	}
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	
	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}
	
	public function addNumberOfUnits( float $number_of_units ) : void
	{
		$this->number_of_units += $number_of_units;
	}
}