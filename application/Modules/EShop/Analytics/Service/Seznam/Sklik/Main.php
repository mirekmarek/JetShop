<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\Seznam\Sklik;


use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\CashDesk;
use JetApplication\Category;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\Exports_Join_KindOfProduct;
use JetApplication\Order;
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
	protected string $zbozi_id = '';
	protected string $retargeting_id = '';
	
	public function allowed() : bool
	{
		//return Application_Service_EShop::CookieSettings()?->groupAllowed(EShop_CookieSettings_Group::STATS);
		return true;
	}
	
	public function init( EShop $eshop ) : void
	{
		parent::init( $eshop );
		
		$this->id = $this->getEshopConfig($eshop)->getId();
		$this->zbozi_id = $this->getEshopConfig($eshop)->getZboziId();
		$this->retargeting_id = $this->getEshopConfig($eshop)->getRetargetingId();
		
		if( $this->id ) {
			$this->enabled = true;
		}
	}
	
	public function header(): string
	{
		$this->view->setVar('id', $this->id);
		
		return $this->view->render('header');
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
		$this->view->setVar('id', $this->id );
		$this->view->setVar('retargeting_id', $this->retargeting_id );
		
		$category_id = '';
		
		$kind_of_product_id = Category::get( $category->getId() )?->getKindOfProductId();
		if($kind_of_product_id) {
			$join = Exports_Join_KindOfProduct::get('ZboziCZ', $category->getEshop(), $kind_of_product_id);
			$category_id = $join?->getExportCategoryId() ?? '';
		}
		
		$this->view->setVar('category_id', $category_id );
		
		return $this->view->render( 'category' );
		
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
		$this->view->setVar('id', $this->id );
		$this->view->setVar('retargeting_id', $this->retargeting_id );
		$this->view->setVar('product', $product );
		
		return $this->view->render( 'product-detail' );
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
		$this->view->setVar('zbozi_id', $this->zbozi_id);
		$this->view->setVar('order', $order);
		
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
		return 'Seznam Sklik';
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