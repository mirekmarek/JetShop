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
use JetApplication\EShop_CookieSettings_Group;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Analytics_Service;
use JetApplication\EShop_Managers;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\EShops;
use JetApplication\Signpost_EShopData;


class Main extends EShop_Analytics_Service implements EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected string $currency_code;
	protected Pricelist $pricelist;
	protected string $id = '';
	
	public function init() : void
	{
		$this->enabled = true;
		$eshop = EShops::getCurrent();
		$this->id = $this->getEshopConfig( $eshop )->getFacebookId();
		$this->pricelist = Pricelists::getCurrent();
		$this->currency_code = $this->pricelist->getCurrencyCode();
		
		if(
			!EShop_Managers::CookieSettings()?->groupAllowed(EShop_CookieSettings_Group::STATS) ||
			!$this->id
		) {
			$this->enabled = false;
		}
	}
	
	public function header(): string
	{
		if( !$this->enabled ) {
			return '';
		}
		
		$this->view->setVar('id', $this->id);
		
		return $this->view->render('header');
	}
	
	public function generateEvent( string $event, array $event_data=[] ) : string
	{
		if( !$this->enabled ) {
			return '';
		}
		
		$this->view->setVar('event', $event);
		$this->view->setVar('event_data', $event_data);
		
		return $this->view->render('event');
		
	}
	
	
	public function viewProductsList( ProductListing $list, string $category_name='', string $category_id='' ) : string
	{
		$pricelist = Pricelists::getCurrent();
		
		$data = [
			'content_ids' => [],
			'contents' => [],
			'content_type' => 'product',
			'value' => 0.0,
			'currency' => $pricelist->getCurrency()->getCode()
		];
		
		foreach($list->getVisibleProducts() as $product) {
			$data['content_ids'][] = $product->getId();
			$data['value'] += $product->getPrice( $pricelist );
			$data['contents'][] = [
				'id' => $product->getId(),
				'quantity' => 1
			];
		}
		
		
		return $this->generateEvent( 'ViewContent', $data );
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
	
	public function viewCategory( Category_EShopData $category ): string
	{
		return '';
	}
	
	public function customEvent( string $evetnt, array $event_data = [] ): string
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
	
	public function viewSignpost( Signpost_EShopData $signpost ): string
	{
		// TODO: Implement viewSignpost() method.
		return '';
	}
}