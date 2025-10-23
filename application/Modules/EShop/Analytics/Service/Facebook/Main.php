<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\Facebook;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\EShop_CookieSettings_Group;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Pricelists;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\Application_Service_EShop;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;


class Main extends Application_Service_EShop_AnalyticsService implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected string $id = '';
	
	public function allowed() : bool
	{
		return Application_Service_EShop::CookieSettings()?->groupAllowed(EShop_CookieSettings_Group::STATS);
	}
	
	public function init( EShop $eshop ) : void
	{
		parent::init( $eshop );
		
		$this->id = $this->getEshopConfig( $eshop )->getFacebookId();
		
		if( $this->id ) {
			$this->enabled = true;
		}
	}
	
	public function header(): string
	{
		$this->view->setVar('id', $this->id);
		
		return $this->view->render('header');
	}
	
	public function generateEvent( string $event, array $event_data=[] ) : string
	{
		$this->view->setVar('event', $event);
		$this->view->setVar('event_data', $event_data);
		
		return $this->view->render('event');
		
	}
	
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		$pricelist = Pricelists::getCurrent();
		
		$data = [
			'content_ids' => [$product->getId()],
			'contents' => [],
			'content_type' => 'product',
			'value' => $product->getPrice( $pricelist ),
			'currency' => $pricelist->getCurrency()->getCode()
		];
		
		$data['contents'][] = [
			'id' => $product->getId(),
			'quantity' => 1
		];
		
		
		return $this->generateEvent( 'ViewContent', $data );
	}
	
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		$pricelist = Pricelists::getCurrent();
		$product = $new_cart_item->getProduct();
		
		$data = [
			'content_ids' => [$product->getId()],
			'contents' => [],
			'content_type' => 'product',
			'value' => $product->getPrice( $pricelist ),
			'currency' => $pricelist->getCurrency()->getCode()
		];
		
		$data['contents'][] = [
			'id' => $product->getId(),
			'quantity' => $new_cart_item->getNumberOfUnits()
		];
		
		return $this->generateEvent('AddToCart', $data );
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
		return $this->generateEvent('InitiateCheckout', []);
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
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
		$pricelist = Pricelists::getCurrent();
		
		$data = [
			'content_ids' => [],
			'contents' => [],
			'content_type' => 'product',
			'value' => 0.0,
			'currency' => $pricelist->getCurrency()->getCode()
		];
		
		if($product_listing) {
			foreach($product_listing->getVisibleProducts() as $product) {
				$data['content_ids'][] = $product->getId();
				$data['value'] += $product->getPrice( $pricelist );
				$data['contents'][] = [
					'id' => $product->getId(),
					'quantity' => 1
				];
			}
		}
		
		
		return $this->generateEvent( 'ViewContent', $data );
	}
	
	public function customEvent( string $event, array $event_data = [] ): string
	{
		return '';
	}
	
	
	public function purchase( Order $order ) : string
	{
		$pricelist = $order->getPricelist();
		
		$data = [
			'content_ids' => [],
			'content_type' => 'product',
			'value' => 0.0,
			'currency' => $pricelist->getCurrency()->getCode()
		];
		
		foreach($order->getItems() as $item) {
			if(
				$item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
				$item->getType()==Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT
			) {
				$data['content_ids'][] = $item->getItemId();
				$data['value'] += $item->getTotalAmount();
			}
		}
		
		
		return $this->generateEvent('Purchase', $data);
	}
	
	public function viewSignpost( Signpost_EShopData $signpost ): string
	{
		// TODO: Implement viewSignpost() method.
		return '';
	}
	
	
	public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		//TODO:
		return '';
	}
	
	public function search( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		//TODO:
		return '';
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_ANALYTICS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Facebook Pixel';
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