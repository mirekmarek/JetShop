<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\GA4;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Brand_EShopData;
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
	protected bool $native_mode = true;
	protected string $id = '';
	
	public function init() : void
	{
		$this->enabled = true;
		$eshop = EShops::getCurrent();
		$this->id = $this->getEshopConfig( $eshop )->getGoogleId();
		$this->pricelist = Pricelists::getCurrent();
		$this->currency_code = $this->pricelist->getCurrencyCode();
		
		if(
			!EShop_Managers::CookieSettings()?->groupAllowed(EShop_CookieSettings_Group::STATS) ||
			!$this->id
		) {
			$this->enabled = false;
		}
		
	}
	
	
	public function getNativeMode(): bool
	{
		return $this->native_mode;
	}
	
	public function setNativeMode( bool $native_mode ): void
	{
		$this->native_mode = $native_mode;
	}
	
	
	protected function generateEvent_dataLayer( string $event, array $event_data ) : string {
		if( !$this->enabled ) {
			return '';
		}
		
		
		$this->view->setVar('event', $event);
		$this->view->setVar('event_data', $event_data);
		
		return $this->view->render('dataLayer/event');
	}
	
	
	protected function generateEvent_native( string $event, array $event_data ) : string
	{
		if( !$this->enabled ) {
			return '';
		}
		
		$this->view->setVar('event', $event);
		$this->view->setVar('event_data', $event_data);
		
		return $this->view->render('native/event');
	}
	
	public function generateEvent( string $event, array $event_data ) : string
	{
		if( !$this->enabled ) {
			return '';
		}
		
		if($this->native_mode) {
			return $this->generateEvent_native( $event, $event_data );
		} else {
			return $this->generateEvent_dataLayer( $event, $event_data );
		}
	}
	
	protected function productToItem( Product_EShopData $product ) : array
	{
		$discount = $product->getDiscountPercentage( $this->pricelist );
		$brand = Brand_EShopData::get( $product->getBrandId() );
		
		
		return [
			'item_id' => $product->getId(),
			'item_name' => $product->getName(),
			'currency' => $this->currency_code,
			'discount' => $discount,
			'index' => 0,
			'item_brand' => $brand?->getName(),
			'item_category' => $product->getKind()?->getName()??'',
			'item_list_id' => '',
			'item_list_name' => '',
			'price' => $product->getPrice( $this->pricelist ),
			'quantity' => 1
		];
	}
	
	public function header(): string
	{
		if(!$this->id) {
			return '';
		}
		
		$this->view->setVar('id', $this->id);
		if(!$this->enabled):
			return $this->view->render('header/disabled');
		endif;
		
		return $this->view->render('header/enabled');
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
	
	
	public function viewProductsList( ProductListing $list, string $category_name='', string $category_id='' ) : string
	{
		$category_id = $category_id??0;
		$category_name = $category_name??'';
		
		$items = [];
		
		$i = -1;
		foreach($list->getVisibleProducts() as $product) {
			$i++;
			
			$item = static::productToItem( $product );
			$item['index'] = $i;
			
			$items[] = $item;
		}
		
		return $this->generateEvent('view_item_list', [
			'item_list_id' => $category_id,
			'item_list_name' => $category_name,
			'items' =>$items
		]);
	}
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		$item = static::productToItem( $product );
		
		return $this->generateEvent('view_item', [
			'currency' => $this->currency_code,
			'value' => $product->getPrice( $this->pricelist ),
			'items' =>[$item]
		]);
	}
	
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		$item = static::productToItem( $new_cart_item->getProduct() );
		$item['quantity'] = $new_cart_item->getNumberOfUnits();
		
		return $this->generateEvent('add_to_cart', [
			'currency' => $this->currency_code,
			'value' => $new_cart_item->getAmount(),
			'items' => [$item]
		]);
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ) : string
	{
		$item = static::productToItem( $cart_item->getProduct() );
		$item['quantity'] = $cart_item->getNumberOfUnits();
		
		return $this->generateEvent('remove_from_cart', [
			'currency' => $this->currency_code,
			'value' => $cart_item->getAmount(),
			'items' =>[$item]
		]);
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		$data = [
			'currency' => $this->currency_code,
			'value' => $cart->getAmount(),
			'items' => []
		];
		
		foreach($cart->getItems() as $cart_item) {
			$item = static::productToItem( $cart_item->getProduct() );
			$item['quantity'] = $cart_item->getNumberOfUnits();
			
			$data['items'][] = $item;
		}
		
		return $this->generateEvent('view_cart', $data );
	}
	
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		$data = [
			'items' => []
		];
		
		foreach($cash_desk->getOrder()->getItems() as $order_item) {
			
			if(
				$order_item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
				$order_item->getType()==Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT
			) {
				$item = static::productToItem( Product_EShopData::get($order_item->getItemId()) );
				$item['quantity'] = $order_item->getNumberOfUnits();
				$data['items'][] = $item;
			}
			
		}
		
		return $this->generateEvent('begin_checkout', $data );
		
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		
		$res = '';
		$order = $cash_desk->getOrder();
		$data = [
			'currency' => $this->currency_code,
			'value' => '',
			'shipping_tier' => 0,
			'items' => []
		];
		
		foreach($cash_desk->getOrder()->getItems() as $order_item) {
			if($order_item->getType()==Order_Item::ITEM_TYPE_DELIVERY) {
				$data['value'] = $order_item->getTotalAmount();
				$data['shipping_tier'] = $order_item->getItemCode();
			}
			
			if(
				$order_item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
				$order_item->getType()==Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT
			) {
				$item = static::productToItem( Product_EShopData::get($order_item->getItemId()) );
				$item['quantity'] = $order_item->getNumberOfUnits();
				$data['items'][] = $item;
			}
			
		}
		
		$res .=  $this->generateEvent('add_shipping_info', $data );
		
		$order = $cash_desk->getOrder();
		$data = [
			'currency' => $this->currency_code,
			'value' => '',
			'shipping_tier' => 0,
			'items' => []
		];
		
		foreach($cash_desk->getOrder()->getItems() as $order_item) {
			if($order_item->getType()==Order_Item::ITEM_TYPE_PAYMENT) {
				$data['value'] = $order_item->getTotalAmount();
				$data['payment_type'] = $order_item->getItemCode();
			}
			
			if(
				$order_item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
				$order_item->getType()==Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT
			) {
				$item = static::productToItem( Product_EShopData::get($order_item->getItemId()) );
				$item['quantity'] = $order_item->getNumberOfUnits();
				$data['items'][] = $item;
			}
		}
		
		$res .= $this->generateEvent('add_payment_info', $data );
		
		return $res;
		
	}

	
	public function purchase( Order $order ) : string
	{
		$data = [
			'transaction_id' => $order->getNumber(),
			'currency' => $this->currency_code,
			'value' => $order->getTotalAmount_WithoutVAT(),
			'tax' => 0.0,
			'shipping' => 0.0,
			'items' => []
		];
		
		foreach($order->getItems() as $order_item) {
			
			$vat = 0;
			if($order_item->getVatRate()) {
				$mtp = 1+($order_item->getVatRate()/100);
				$wo_vat = $order_item->getTotalAmount()/$mtp;
				$vat = $order_item->getTotalAmount() - $wo_vat;
			}
			
			$data['tax']+=$vat;
			
			if($order_item->getType()==Order_Item::ITEM_TYPE_DELIVERY) {
				$data['shipping'] += $order_item->getTotalAmount();
			}
			
			
			if(
				$order_item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
				$order_item->getType()==Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT
			) {
				$item = static::productToItem( Product_EShopData::get($order_item->getItemId()) );
				$item['quantity'] = $order_item->getNumberOfUnits();
				$data['items'][] = $item;
			}
		}
		
		return $this->generateEvent('purchase', $data );
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_ANALYTICS;
	}
	
	
	public function getControlCentreTitle(): string
	{
		return 'Google Analytics v4';
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