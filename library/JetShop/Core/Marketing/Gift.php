<?php
namespace JetShop;

use JetApplication\MeasureUnit;
use JetApplication\Product_EShopData;
use JetApplication\ShoppingCart;

class Core_Marketing_Gift {
	
	protected ShoppingCart $cart;
	
	protected int $product_id;
	
	protected float $number_of_units;
	
	protected int $in_stock_limit = 0;
	
	protected bool $auto_append = false;
	
	protected bool $only_one = false;
	
	protected ?Product_EShopData $product = null;
	
	public function __construct( ShoppingCart $cart, int $product_id, float $number_of_units )
	{
		$this->cart = $cart;
		$this->product_id = $product_id;
		$this->setNumberOfUnits( $number_of_units );
	}
	
	public function getNumberOfUnits(): float
	{
		return $this->number_of_units;
	}
	
	public function getMeasureUnit() : ?MeasureUnit
	{
		return $this->getProduct()->getKind()?->getMeasureUnit();
	}
	
	public function setNumberOfUnits( float $number_of_units ): void
	{
		if($this->getMeasureUnit()) {
			$number_of_units = $this->getMeasureUnit()->round( $number_of_units );
		}
		
		$this->number_of_units = $number_of_units;
	}
	
	
	public function setInStockLimit( int $in_stock_limit ) : void
	{
		$this->in_stock_limit = $in_stock_limit;
	}
	
	public function getInStockLimit() : int
	{
		return $this->in_stock_limit;
	}
	
	public function setProductId( int $product_id ) : void
	{
		$this->product_id = $product_id;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function getProduct() : Product_EShopData
	{
		if($this->product===null) {
			$this->product = Product_EShopData::get( $this->product_id, $this->cart->getEshop() );
		}
		return $this->product;
	}
	
	public function getAutoAppend() : bool
	{
		return $this->auto_append;
	}
	
	public function setAutoAppend( bool $auto_append ) : void
	{
		$this->auto_append = $auto_append;
	}
	

	public function getOnlyOne(): bool
	{
		return $this->only_one;
	}
	
	public function setOnlyOne( bool $only_one ): void
	{
		$this->only_one = $only_one;
	}
	
	public function checkInStockLimit() : bool
	{
		if($this->in_stock_limit==0) {
			return true;
		}
		
		$in_stock_qty = $this->getProduct()->getNumberOfAvailable( $this->cart->getAvailability() );
		
		if( $in_stock_qty<$this->number_of_units ) {
			$this->number_of_units = $in_stock_qty;
		}
		
		if($this->number_of_units<1) {
			return false;
		}
		
		return true;
	}
	
	
}