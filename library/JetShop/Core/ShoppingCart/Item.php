<?php
namespace JetShop;

use JetApplication\Product_ShopData;
use JetApplication\ShoppingCart;

abstract class Core_ShoppingCart_Item
{

	protected ?ShoppingCart $__cart = null;

	protected int $product_id = 0;

	protected int $quantity = 0;

	protected string $check_error_message = '';
	
	protected ?Product_ShopData $product=null;

	public function __construct( int $product_id, int $quantity )
	{
		$this->product_id = $product_id;
		$this->quantity = $quantity;
	}

	public function getCart() : ShoppingCart
	{
		return $this->__cart;
	}

	public function setCart( ShoppingCart $cart ) : void
	{
		$this->__cart = $cart;
	}

	public function getProductId() : int
	{
		return $this->product_id;
	}

	public function getProduct() : ?Product_ShopData
	{
		if($this->product===null) {
			$this->product = Product_ShopData::get( $this->product_id, $this->getCart()->getShop() );
		}
		
		return $this->product;
	}

	public function getQuantity() : int
	{
		return $this->quantity;
	}

	public function isValid() : bool
	{
		if(
			!$this->product_id ||
			!$this->getProduct() ||
			!$this->getProduct()->isActive()
		) {
			return false;
		}
		
		return true;
	}

	public function setQuantity( int $quantity ) : bool
	{
		if(!$this->checkQuantity($quantity)) {
			return false;
		}

		$this->quantity = $quantity;

		return true;

	}

	public function getAmount() : float|int
	{
		return $this->quantity * $this->getProduct()->getPrice();
	}

	public function getCheckErrorMessage() : string
	{
		return $this->check_error_message;
	}

	public function setCheckErrorMessage( string $check_error_message ) : void
	{
		$this->check_error_message = $check_error_message;
	}
	
	public function checkQuantity( int $quantity, bool $generate_error_message=false ) : bool
	{
		//TODO:
		return true;
	}

}