<?php
namespace JetShop;

use Jet\Db;
use Jet\Mvc_Page;
use Jet\Mvc_Site;

abstract class Core_ShoppingCart
{
	protected string $id = '';

	protected string $shop_id = '';

	/**
	 * @var ShoppingCart_Item[]
	 */
	protected array $items = [];

	protected bool $_updated = false;

	protected static string $default_cart_db_connection = '';

	protected static string $default_database_table_name = 'shopping_carts';

	protected string $cart_db_connection = '';

	protected string $database_table_name = 'carts';

	protected static ?ShoppingCart $cart = null;

	public static function get() : ShoppingCart
	{
		if(!static::$cart) {
			if(!isset($_COOKIE['cart_id'])) {
				$_COOKIE['cart_id'] = uniqid().uniqid().uniqid();
			}

			static::$cart = new ShoppingCart( Shops::getCurrentId() );
			static::$cart->setId( $_COOKIE['cart_id'] );
			static::$cart->setShopId( Shops::getCurrentId() );
		}

		return static::$cart;
	}

	public static function getDefaultCartDbConnection() : string
	{
		return self::$default_cart_db_connection;
	}

	public static function setDefaultCartDbConnection( string $default_cart_db_connection ) : void
	{
		self::$default_cart_db_connection = $default_cart_db_connection;
	}

	public static function getDefaultDatabaseTableName() : string
	{
		return self::$default_database_table_name;
	}

	public static function setDefaultDatabaseTableName( string $default_database_table_name ) : void
	{
		self::$default_database_table_name = $default_database_table_name;
	}

	public function __construct( string $shop_id )
	{
		$this->shop_id = $shop_id;

		$this->cart_db_connection = static::$default_cart_db_connection;
		$this->database_table_name = static::$default_database_table_name;

	}

	public function getCartDbConnection() : string
	{
		return $this->cart_db_connection;
	}

	public function setCartDbConnection( string $cart_db_connection ) : void
	{
		$this->cart_db_connection = $cart_db_connection;
	}

	public function getDatabaseTableName() : string
	{
		return $this->database_table_name;
	}

	public function setDatabaseTableName( string $database_table_name ) : void
	{
		$this->database_table_name = $database_table_name;
	}

	public function getShopId() : string
	{
		return $this->shop_id;
	}

	public function setShopId( string $shop_id ): void
	{
		$this->shop_id = $shop_id;
	}

	public function getId() : string
	{
		return $this->id;
	}

	public function setId( string $id ) : void
	{

		$this->id = $id;

		setcookie(
			'cart_id',
			$this->id,
			time() + (10 * 365 * 24 * 60 * 60),
			'/'
		);

		$_COOKIE['cart_id'] = $this->id;

		$this->load();
	}

	protected function load() : void
	{
		/**
		 * @var ShoppingCart $cart
		 */
		$cart = $this;

		$items = Db::get( $this->cart_db_connection )->fetchOne("SELECT items FROM ".$this->database_table_name." WHERE id='".addslashes($this->id)."' and shop_id='{$this->shop_id}'");

		if(!$items) {
			return;
		}

		$items = unserialize($items);
		if(!$items) {
			return;
		}


		$this->items = [];

		/**
		 * @var ShoppingCart_Item[] $items
		 */
		foreach( $items as $item ) {
			$item->setCart( $cart );

			if(!$item->isValid()) {
				continue;
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
					continue;
				}
			}

			$this->items[$item->getProductId()] = $item;
		}
	}

	public function save() : void
	{

		$items = addslashes(serialize($this->items));
		$count = count($this->items);

		Db::get( $this->cart_db_connection )->execCommand("INSERT INTO ".$this->database_table_name." SET
                        id='{$this->id}',
                        shop_id='{$this->shop_id}',
                        last_activity_date_time=now(),
                        items='{$items}',
                        items_count={$count}
                    ON DUPLICATE KEY UPDATE
                        last_activity_date_time=now(),
                        items='{$items}',
                        items_count={$count}
                        ");

	}

	public function getItemQuantity( int $product_id ) : int
	{

		$product_id = (int)$product_id;

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
	 * @return Product[]
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
		$product_id = (int)$product_id;
		$quantity = (int)$quantity;

		if(!isset($this->items[$product_id])) {
			return false;
		}

		$item = $this->items[$product_id];

		if(!$item->checkQuantity($quantity, true)) {
			$error_message = $item->getCheckErrorMessage();
			return false;
		}

		$item->setQuantity( $quantity );
		$this->save();

		return true;
	}

	public function addItem( int $product_id, int $quantity, string &$error_message='' ) : bool|shoppingCart_item
	{

		$product_id = (int)$product_id;
		$quantity = (int)$quantity;

		$item = new ShoppingCart_Item( $product_id, $quantity );
		/** @noinspection PhpParamsInspection */
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

		$this->save();

		return $item;
	}

	public function removeItem( int $product_id ) : void
	{

		if(!isset($this->items[$product_id])) {
			return;
		}
		unset($this->items[$product_id]);

		$this->save();
	}

	public function reset() : void
	{
		$this->items = [];

		$this->save();

	}

	/**
	 * @return Order_Item[]
	 */
	public function getOrderItems() : array
	{
		$items = [];
		foreach( $this->getItems() as $cart_item ) {

			$product_sq = $cart_item->getProduct()->getStockStatus( $this->shop_id );
			$cart_q = $cart_item->getQuantity();


			if(
				$cart_q>$product_sq &&
				$product_sq>0
			) {
				$item = new Order_Item();
				$item->setDataByCartItem( $cart_item, $product_sq, true );
				$items[] = $item;

				$item = new Order_Item();
				$item->setDataByCartItem( $cart_item, $cart_q - $product_sq, false );
				$items[] = $item;


			} else {
				$item = new Order_Item();
				$item->setDataByCartItem( $cart_item, $cart_q, $product_sq>0 );
				$items[] = $item;
			}
		}

		return $items;

	}

	public static function getCartURL(): string
	{
		$shop = Shops::getCurrent();

		return Mvc_Page::get('shopping-cart', $shop->getLocale(), $shop->getSiteId())->getURL();
	}

}