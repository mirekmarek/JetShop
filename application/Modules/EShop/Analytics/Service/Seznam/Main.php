<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\Seznam;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\EShop_CookieSettings_Group;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Order;
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
		$this->id = $this->getEshopConfig($eshop)->getSeznamId();
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
		if(
			!$this->id ||
			!$this->enabled
		) {
			return '';
		}
		
		$this->view->setVar('id', $this->id);
		
		return $this->view->render('header');
	}
	
	public function generateEvent( string $event, array $event_data=[] ) : string
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
	
	public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing = null ): string
	{
		return '';
	}
	
	public function customEvent( string $evetnt, array $event_data = [] ): string
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
		return $this->generateEvent('InitiateCheckout', []);
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		return '';
	}
	
	public function purchase( Order $order ) : string
	{
		return '';
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