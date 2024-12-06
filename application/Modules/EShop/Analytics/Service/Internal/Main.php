<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\EShop\Analytics\Service\Internal;

use Jet\Session;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\Order;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Analytics_Service;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\EShops;
use JetApplication\Statistics_Category_ViewLog;
use JetApplication\Statistics_Order_SourceLog;
use JetApplication\Statistics_Product_OrderLog;
use JetApplication\Statistics_Product_ViewLog;

/**
 *
 */
class Main extends EShop_Analytics_Service
{
	protected string $currency_code;
	protected Pricelist $pricelist;
	protected Session $sesion;
	
	public function init() : void
	{
		$this->enabled = true;
		$eshop = EShops::getCurrent();
		
		$this->pricelist = Pricelists::getCurrent();
		$this->currency_code = $this->pricelist->getCurrencyCode();
		$this->sesion = new Session('internal_analytics');
	}
	
	public function catchConversionSourceInfo() : void
	{
		if(!$this->sesion->getValueExists('source_info')) {
			$info = new Statistics_Order_SourceLog();

			$this->sesion->setValue('source_info', $info);
		}
	}
	
	
	public function viewCategory( Category_EShopData $category ): string
	{
		Statistics_Category_ViewLog::rec( $category );
		
		return '';
	}

	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		
		register_shutdown_function( function() use ($product) {
			Statistics_Product_ViewLog::rec( $product );
		} );
		
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		$source_info = $this->sesion->getValue('source_info');
		
		if($source_info && $source_info instanceof Statistics_Order_SourceLog) {
			$source_info->setOrder( $order );
			$source_info->setIsNew();
			$source_info->save();
		}
		
		Statistics_Product_OrderLog::rec( $order );

		return '';
	}
	
	
	
	public function viewProductsList( array $list, ?Category_EShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string
	{
		return '';
	}
	
	
	public function header(): string
	{
		return '';
	}
	
	public function generateEvent( string $event, array $event_data=[] ) : string
	{
		return '';
	}
	
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		return '';
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ) : string
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
	
	
	public function documentStart(): string
	{
		return '';
	}
	
	public function documentEnd(): string
	{
		return '';
	}
	
	public function customEvent( string $evetnt, array $event_data = [] ): string
	{
		return '';
	}
}