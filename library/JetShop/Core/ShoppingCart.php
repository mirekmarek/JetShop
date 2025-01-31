<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Availability;
use JetApplication\Marketing_AutoOffer;
use JetApplication\Marketing_Gift;
use JetApplication\Marketing_Gift_Product;
use JetApplication\Marketing_Gift_ShoppingCart;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShop;
use JetApplication\ShoppingCart_Item;


abstract class Core_ShoppingCart
{
	protected string $id = '';

	protected ?EShop $eshop = null;
	protected ?Availability $availability = null;
	protected ?Pricelist $pricelist = null;

	/**
	 * @var ShoppingCart_Item[]
	 */
	protected array $items = [];
	
	protected array $selected_cart_gift_ids = [];
	
	

	public function __construct( EShop $eshop, Availability $availability, Pricelist $pricelist )
	{
		$this->eshop = $eshop;
		$this->availability = $availability;
		$this->pricelist = $pricelist;
	}

	
	public function getEshop() : EShop
	{
		return $this->eshop;
	}

	public function getAvailability(): ?Availability
	{
		return $this->availability;
	}
	
	
	public function getPricelist(): ?Pricelist
	{
		return $this->pricelist;
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
		
		$q = $item->getNumberOfUnits();
		
		if(!$item->setNumberOfUnits( $q )) {
			do {
				$q--;
				
				if( $item->setNumberOfUnits( $q ) ) {
					break;
				}
				
			} while($q>0);
			
			if($q<=0) {
				return;
			}
		}
		
		$this->items[$item->getProductId()] = $item;
	}
	
	public function getItemNumberOfUnits( int $product_id ) : float
	{

		if( isset($this->items[$product_id] )) {
			return $this->items[$product_id]->getNumberOfUnits();
		}

		return 0;
	}

	public function getNumberOfUnits() : float
	{

		$q = 0;

		foreach( $this->getItems() as $item ) {
			$q += $item->getNumberOfUnits();
		}

		return $q;
	}


	/**
	 * @return Product_EShopData[]
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

	public function setNumberOfUnits( int $product_id, float $number_of_units, string &$error_message='' ) : bool
	{

		if(!isset($this->items[$product_id])) {
			return false;
		}

		if($number_of_units<1) {
			$this->removeItem($product_id);
			return true;
		}

		$item = $this->items[$product_id];

		$res =  $item->setNumberOfUnits( $number_of_units );
		
		$error_message = $item->getCheckErrorMessage();
		
		return $res;
	}
	
	public function selectAutoOffer( Marketing_AutoOffer $auto_offer, float $number_of_units, string &$error_message='' ) : bool|shoppingCart_item
	{
		$product_id = $auto_offer->getOfferProductId();
		
		$product = Product_EShopData::get( $product_id, $this->eshop );
		
		$item = new ShoppingCart_Item(
			product_id: $product_id,
			number_of_units: $number_of_units,
			measure_unit: $product->getKind()?->getMeasureUnit()
		);
		
		$item->setAutoOfferId( $auto_offer->getId() );
		
		$item->setCart( $this );
		
		if( !$item->isValid() ) {
			$error_message = $item->getCheckErrorMessage();
			
			return false;
		}
		
		if(isset( $this->items[$product_id] )) {
			
			$new_number_of_units = $this->items[$product_id]->getNumberOfUnits() + $number_of_units;
			
			if(!$this->setNumberOfUnits( $product_id, $new_number_of_units, $error_message )) {
				return false;
			}
			
			return $this->items[$product_id];
		}
		
		
		if(!$item->checkQuantity( $number_of_units )) {
			$error_message = $item->getCheckErrorMessage();
			
			if($number_of_units<=0) {
				return false;
			}
		}
		
		$this->items[$product_id] = $item;
		
		return $item;
	}
	

	public function addItem( Product_EShopData $product, float $number_of_units, ?int $selected_gift_id=null, string &$error_message='' ) : bool|shoppingCart_item
	{
		$selected_gift_id = 0;
		$gifts = $this->getAvailableProductGifts( $product );
		foreach($gifts as $gift) {
			$selected_gift_id = $gift->getGiftProductId();
			break;
		}
		
		$product_id = $product->getId();

		$item = new ShoppingCart_Item(
			product_id: $product_id, 
			number_of_units: $number_of_units, 
			measure_unit: $product->getKind()?->getMeasureUnit(), 
			selected_gift_id: $selected_gift_id 
		);

		$item->setCart( $this );


		if( !$item->isValid() ) {
			$error_message = $item->getCheckErrorMessage();

			return false;
		}

		if(isset( $this->items[$product_id] )) {

			$new_number_of_units = $this->items[$product_id]->getNumberOfUnits() + $number_of_units;

			if(!$this->setNumberOfUnits( $product_id, $new_number_of_units, $error_message )) {
				return false;
			}
			
			$error_message = $this->items[$product_id]->getCheckErrorMessage();

			return $this->items[$product_id];
		}


		if(!$item->checkQuantity( $number_of_units )) {
			$error_message = $item->getCheckErrorMessage();

			if($number_of_units<=0) {
				return false;
			}
		}

		$this->items[$product_id] = $item;

		return $item;
	}

	public function removeItem( int $product_id ) : ?ShoppingCart_Item
	{

		if(!isset($this->items[$product_id])) {
			return null;
		}
		$removed = $this->items[$product_id];
		
		unset($this->items[$product_id]);
		
		return $removed;
	}

	public function reset() : void
	{
		$this->items = [];
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	/**
	 * @return Marketing_Gift_ShoppingCart[]
	 */
	public function getAvailableCartGifts() : array
	{
		$available = Marketing_Gift_ShoppingCart::getAvailable( $this );
		
		$gifts = [];
		
		foreach($available as $id=>$gift) {
			if($gift->isRelevantForCart( $this )) {
				$gifts[$id] = $gift;
			}
		}
		
		return $gifts;
	}
	
	/**
	 * @return Marketing_Gift_Product[]
	 */
	public function getAvailableProductGifts( Product_EShopData $product ) : array
	{
		$gifts = [];
		
		$available = Marketing_Gift_Product::getAvailable( $this );
		foreach($available as $id=>$gift) {
			if($gift->productIsRelevant( $this, $product)) {
				$gifts[$id] = $gift;
			}
		}
		
		return $gifts;
	}
	
	public function selectCartGift( int $gift_product_id ) : void
	{
		if(in_array($gift_product_id, $this->selected_cart_gift_ids)) {
			return;
		}
		
		$available = Marketing_Gift_ShoppingCart::getAvailable( $this );
		if(
			!isset($available[$gift_product_id]) ||
			!$available[$gift_product_id]->isRelevantForCart( $this ) ||
			$available[$gift_product_id]->getAutoAppend()
		) {
			return;
		}

		$this->selected_cart_gift_ids[] = $gift_product_id;
	}
	
	public function unselectCartGift( int $gift_product_id ) : void
	{
		$selected_cart_gift_ids = [];
		foreach($this->selected_cart_gift_ids as $id) {
			if($id!=$gift_product_id) {
				$selected_cart_gift_ids[] = $id;
			}
		}
		$this->selected_cart_gift_ids = $selected_cart_gift_ids;
	}
	
	public function setSelectedCartGifts( array $gift_product_ids ) : void
	{
		$this->selected_cart_gift_ids = [];
		foreach($gift_product_ids as $id) {
			$this->selectCartGift( $id );
		}
	}
	
	public function getSelectedCartGiftIds() : array
	{
		return $this->selected_cart_gift_ids;
	}
	
	/**
	 * @return Marketing_Gift_ShoppingCart[]
	 */
	public function getSelectedCartGifts() : array
	{
		$result = [];
		$available = Marketing_Gift_ShoppingCart::getAvailable( $this );
		
		foreach($this->selected_cart_gift_ids as $id) {
			$result[$id] = $available[$id];
		}
		
		return $result;
	}
	
	/**
	 * @return Marketing_Gift_Product[]
	 */
	public function getSelectedProductGifts() : array
	{
		$result = [];
		foreach($this->getItems() as $item) {
			$item_gift = $item->getSelectedGift();
			if($item_gift) {
				$result[] = $item_gift;
			}
		}
		
		return $result;
	}
	
	/**
	 * @return Marketing_Gift[]
	 */
	public function getAllSelectedGifts() : array
	{
		/**
		 * @var Marketing_Gift[] $res
		 */
		$res = [];
		
		$add = function( Marketing_Gift $gift ) use (&$res) {
			$id = $gift->getProductId();
			
			if($gift->getOnlyOne()) {
				$gift->setNumberOfUnits( 1 );
			}
			
			if(!isset($res[$id])) {
				$res[$id] = $gift;
				return;
			}
			
			if($gift->getOnlyOne() || $res[$id]->getOnlyOne()) {
				$res[$id]->setOnlyOne( true );
				$res[$id]->setNumberOfUnits( 1 );
				return;
			}
			
			if($gift->getInStockLimit()>0) {
				if(
					$res[$id]->getInStockLimit()==0 ||
					$gift->getInStockLimit()<$res[$id]->getInStockLimit()
				) {
					$res[$id]->setInStockLimit( $gift->getInStockLimit() );
				}
			}
			
		};
		
		$available = Marketing_Gift_ShoppingCart::getAvailable( $this );
		
		foreach($available as $gift) {
			if(
				$gift->getAutoAppend() &&
				$gift->isRelevantForCart($this)
			) {
				$add( $gift->createCommonGift( $this ) );
			}
		}
		
		foreach($this->getSelectedCartGifts() as $gift) {
			$add( $gift->createCommonGift( $this ) );
		}
		
		foreach($this->getItems() as $item) {
			if(($gift=$item->getSelectedGift())) {
				$add( $gift->createCommonGift( $this, $item->getNumberOfUnits() ) );
			}
		}
		
		foreach( $res as $id=>$gift ) {
			if(!$gift->checkInStockLimit()) {
				unset($res[$id]);
			}
		}
		
		return $res;
	}

}