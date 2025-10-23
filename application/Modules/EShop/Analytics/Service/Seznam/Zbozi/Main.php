<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\Seznam\Zbozi;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\EShop_CookieSettings_Group;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Product_EShopData;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;


class Main extends Application_Service_EShop_AnalyticsService implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected string $id = '';
	protected string $key = '';
	protected bool $test_mode = false;
	
	public function allowed() : bool
	{
		return Application_Service_EShop::CookieSettings()?->groupAllowed(EShop_CookieSettings_Group::MARKETING);
	}
	
	public function init( EShop $eshop ) : void
	{
		parent::init( $eshop );
		
		$this->id = $this->getEshopConfig($eshop)->getId();
		$this->key = $this->getEshopConfig($eshop)->getKey();
		
		if( $this->id ) {
			$this->enabled = true;
		}
	}
	
	public function initTest( EShop $eshop ) : void
	{
		$this->init( $eshop );
		$this->test_mode = true;
	}
	
	public function header(): string
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
	
	public function viewHomePage() : string
	{
		return '';
	}
	
	public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing = null ): string
	{
		return '';
	}
	
	public function customEvent( string $event, array $event_data = [] ): string
	{
		return '';
	}
	
	public function viewProductsList( ProductListing $list, string $category_name='', string $category_id='' ) : string
	{
		return '';
	}
	
	public function viewProductDetail( Product_EShopData $product ) : string
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
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		$this->view->setVar('id', $this->id);
		$this->view->setVar('key', $this->key);
		
		$this->view->setVar('order', $order);
		
		try {
			$zbozi = new ZboziKonverze($this->id, $this->key);
			$zbozi->useSandbox( $this->test_mode );
			
			
			$discounts = 0;
			$delivery_price = 0;
			$payment_method_title = '';
			
			foreach($order->getItems() as $item){
				switch($item->getType()){
					case Order_Item::ITEM_TYPE_PRODUCT:
					case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
						$zbozi->addCartItem(array(
							"itemId" => $item->getItemId(),
							"productName" => $item->getTitle(),
							"quantity" => $item->getNumberOfUnits(),
							"unitPrice" => $item->getPricePerUnit(),
						));
						
						break;
					case Order_Item::ITEM_TYPE_DISCOUNT:
						$discounts += $item->getTotalAmount();
						break;
					case Order_Item::ITEM_TYPE_DELIVERY:
						$delivery_price += $item->getTotalAmount();
						break;
				}
			}
			
			
			$zbozi->setOrder(array(
				"orderId" => $order->getNumber(),
				"email" => $order->getEmail(),
				"deliveryType" => "VLASTNI_PREPRAVA",
				"deliveryPrice" => $delivery_price,
				"otherCosts" => $discounts,
				"paymentType" => $order->getPaymentMethod()?->getTitle()??'',
			));
			

			$zbozi->send();
			
		} catch (ZboziKonverze_Exception $e) {
		}
		
		return $this->view->render( 'purchase' );
	}
	
	
	
	public function viewSignpost( Signpost_EShopData $signpost ): string
	{
		return '';
	}
	
	public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		return '';
	}
	
	public function search( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		return '';
	}
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_ANALYTICS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Seznam Zboží';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'chart-line';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
}