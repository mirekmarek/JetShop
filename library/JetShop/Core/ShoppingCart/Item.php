<?php
namespace JetShop;

use JetApplication\ShoppingCart;
use JetApplication\Product;

abstract class Core_ShoppingCart_Item
{

	protected ?ShoppingCart $__cart = null;

	protected int $product_id = 0;

	protected int $quantity = 0;

	protected ?float $__forced_price_per_item = null;

	protected string $check_error_message = '';

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

	public function getProduct() : Product
	{
		return Product::get( $this->product_id );
	}

	public function getQuantity() : int
	{
		return $this->quantity;
	}

	public function isValid() : bool
	{
		if(!$this->product_id) {
			return false;
		}

		$product = Product::get( $this->product_id );
		if(!$product) {
			return false;
		}

		if(
			!$product->isActive() ||
			!$product->getShopData()->isActive()
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
		return $this->quantity * $this->getProduct()->getFinalPrice();
	}

	public function getCheckErrorMessage() : string
	{
		return $this->check_error_message;
	}

	public function setCheckErrorMessage( string $check_error_message ) : void
	{
		$this->check_error_message = $check_error_message;
	}

	public function getForcedPricePerItem() : float
	{
		return $this->__forced_price_per_item;
	}

	public function setForcedPricePerItem( float $_forced_price_per_item ) : void
	{
		$this->__forced_price_per_item = $_forced_price_per_item;
	}

	public function getPricePerItem() : float
	{
		if($this->__forced_price_per_item!==null) {
			return $this->__forced_price_per_item;
		}

		return $this->getProduct()->getFinalPrice( $this->__cart->getShop() );
	}

	public function checkQuantity( int $quantity, bool $generate_error_message=false ) : bool
	{
		//TODO:
		return true;
	}

}