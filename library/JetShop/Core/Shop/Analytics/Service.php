<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Module_Manifest;
use Jet\Factory_MVC;
use Jet\MVC_View;
use JetApplication\CashDesk;
use JetApplication\Category_ShopData;
use JetApplication\Order;
use JetApplication\Product_ShopData;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;

abstract class Core_Shop_Analytics_Service extends Application_Module {
	
	protected bool $enabled = false;
	protected MVC_View $view;
	
	public function __construct( Application_Module_Manifest $manifest )
	{
		parent::__construct( $manifest );
		$this->view = Factory_MVC::getViewInstance( $this->getViewsDir() );
	}
	
	abstract public function init() : void;
	
	
	public function getEnabled(): bool
	{
		return $this->enabled;
	}
	
	
	public function setEnabled( bool $enabled ): void
	{
		$this->enabled = $enabled;
	}
	
	
	
	public function header() : string
	{
		return '';
	}
	
	public function documentStart() : string
	{
		return '';
	}
	
	public function documentEnd() : string
	{
		return '';
	}
	
	/**
	 * @param array $list
	 * @param Category_ShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	public function viewProducts( array $list, ?Category_ShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string
	{
		return '';
	}
	
	public function viewProduct( Product_ShopData $product ) : string
	{
		return '';
	}
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		return '';
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ): string
	{
		return '';
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		return '';
	}
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function addDeliveryInfo( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function addPaymentInfo( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		return '';
	}
	
	abstract public function generateEvent( string $event, array $event_data ) : string;
	
}