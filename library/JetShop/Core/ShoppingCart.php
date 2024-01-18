<?php
namespace JetShop;

use JetApplication\Product_ShopData;
use JetApplication\Shops_Shop;
use JetApplication\ShoppingCart_Item;


abstract class Core_ShoppingCart
{
	protected string $id = '';

	protected ?Shops_Shop $shop = null;

	/**
	 * @var ShoppingCart_Item[]
	 */
	protected array $items = [];
	

	public function __construct( Shops_Shop $shop )
	{
		$this->shop = $shop;
	}



	public function getShop() : Shops_Shop
	{
		return $this->shop;
	}


	public function getId() : string
	{
		return $this->id;
	}

	public function setId( string $id ) : void
	{
		$this->id = $id;
	}
	
	/**
	 * @param ShoppingCart_Item[] $items
	 */
	public function load( array $items ) : void
	{

		$this->items = [];

		foreach( $items as $item ) {
			$this->loadItem( $item );
		}
	}
	
	protected function loadItem( ShoppingCart_Item $item ) : void
	{
		$item->setCart( $this );
		
		if(!$item->isValid()) {
			return;
		}
		
		$q = $item->getQuantity();
		
		if(!$item->setQuantity( $q )) {
			do {
				$q--;
				
				if( $item->setQuantity( $q ) ) {
					break;
				}
				
			} while($q>0);
			
			if($q<=0) {
				return;
			}
		}
		
		$this->items[$item->getProductId()] = $item;
	}
	
	public function getItemQuantity( int $product_id ) : int
	{

		if( isset($this->items[$product_id] )) {
			return $this->items[$product_id]->getQuantity();
		}

		return 0;
	}

	public function getQuantity() : int
	{

		$q = 0;

		foreach( $this->getItems() as $item ) {
			$q += $item->getQuantity();
		}

		return $q;
	}


	/**
	 * @return Product_ShopData[]
	 */
	public function getProducts() : array
	{

		$products = [];

		foreach( $this->items as $item ) {
			$products[$item->getProductId()] = $item->getProduct();
		}

		return $products;
	}

	public function getProductIds() : array
	{
		$ids = [];

		foreach( $this->items as $item ) {
			$ids[] = $item->getProductId();
		}

		return $ids;

	}

	/**
	 * @return ShoppingCart_Item[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}

	public function getAmount() : float|int
	{
		$amount = 0;

		foreach( $this->getItems() as $item ) {
			$amount += $item->getAmount();
		}

		return $amount;
	}

	public function setQuantity( int $product_id, int $quantity, string &$error_message='' ) : bool
	{

		if(!isset($this->items[$product_id])) {
			return false;
		}

		if($quantity<1) {
			$this->removeItem($product_id);
			return true;
		}

		$item = $this->items[$product_id];

		if(!$item->checkQuantity($quantity, true)) {
			$error_message = $item->getCheckErrorMessage();
			return false;
		}

		$item->setQuantity( $quantity );

		return true;
	}

	public function addItem( int $product_id, int $quantity, string &$error_message='' ) : bool|shoppingCart_item
	{

		$item = new ShoppingCart_Item( $product_id, $quantity );

		$item->setCart( $this );


		if( !$item->isValid() ) {
			$error_message = $item->getCheckErrorMessage();

			return false;
		}

		if(isset( $this->items[$product_id] )) {

			$new_quantity = $this->items[$product_id]->getQuantity() + $quantity;

			if(!$this->setQuantity( $product_id, $new_quantity, $error_message )) {
				return false;
			}

			return $this->items[$product_id];
		}


		if(!$item->checkQuantity( $quantity, true )) {
			$error_message = $item->getCheckErrorMessage();

			return false;
		}

		$this->items[$product_id] = $item;

		return $item;
	}

	public function removeItem( int $product_id ) : void
	{

		if(!isset($this->items[$product_id])) {
			return;
		}
		unset($this->items[$product_id]);
	}

	public function reset() : void
	{
		$this->items = [];
	}

}