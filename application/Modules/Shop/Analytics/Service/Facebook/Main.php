<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Analytics\Service\Facebook;

use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\CashDesk;
use JetApplication\Category_ShopData;
use JetApplication\Shop_CookieSettings_Group;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Pricelists;
use JetApplication\Pricelists_Pricelist;
use JetApplication\Product_ShopData;
use JetApplication\Shop_Analytics_Service;
use JetApplication\Shop_Managers;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Shops;

/**
 *
 */
class Main extends Shop_Analytics_Service implements ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface, Admin_ControlCentre_Module_Interface
{
	use ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	use Admin_ControlCentre_Module_Trait;
	
	protected string $currency_code;
	protected Pricelists_Pricelist $pricelist;
	protected string $id = '';
	
	public function init() : void
	{
		$this->enabled = true;
		$shop = Shops::getCurrent();
		$this->id = $this->getShopConfig( $shop )->getFacebookId();
		$this->pricelist = Pricelists::getCurrent();
		$this->currency_code = $this->pricelist->getCurrencyCode();
		
		if(
			!Shop_Managers::Shop_CookieSettings()?->groupAllowed(Shop_CookieSettings_Group::STATS) ||
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
	
	
	/**
	 * @param Product_ShopData[] $list
	 * @param Category_ShopData|null $category
	 * @param string|null $category_name
	 * @param int|null $category_id
	 * @return string
	 */
	public function viewProducts( array $list, ?Category_ShopData $category=null, ?string $category_name='', ?int $category_id=null ) : string
	{
		$pricelist = Pricelists::getCurrent();
		
		$data = [
			'content_ids' => [],
			'contents' => [],
			'content_type' => 'product',
			'value' => 0.0,
			'currency' => $pricelist->getCurrency()->getCode()
		];
		
		foreach($list as $product) {
			$data['content_ids'][] = $product->getId();
			$data['value'] += $product->getPrice( $pricelist );
			$data['contents'][] = [
				'id' => $product->getId(),
				'quantity' => 1
			];
		}
		
		
		return $this->generateEvent( 'ViewContent', $data );
	}
	
	public function viewProduct( Product_ShopData $product ) : string
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
	
}