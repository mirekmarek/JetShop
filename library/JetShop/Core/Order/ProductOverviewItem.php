<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Product_EShopData;

abstract class Core_Order_ProductOverviewItem
{
	protected int $product_id = 0;
	protected Product_EShopData $product;
	protected float $number_of_units;
	
	public function __construct( Product_EShopData $product, float $number_of_units )
	{
		$this->product = $product;
		$this->product_id = $product->getId();
		$this->number_of_units = $number_of_units;
	}
	
	public function getProduct(): Product_EShopData
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