<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

namespace JetApplication\Installer;

use Error;
use Exception;
use Jet\AJAX;
use Jet\Application_Modules;
use Jet\DataModel_Helper;
use Jet\Http_Request;
use Jet\SysConf_Path;
use Jet\Tr;
use Jet\Translator;
use Jet\UI_messages;
use JetApplication\Application_EShop;
use JetApplication\Availabilities;
use JetApplication\Availability;
use JetApplication\Currencies;
use JetApplication\EShop;
use JetApplication\EShop_Pages;
use JetApplication\EShop_Template;
use JetApplication\EShops;
use JetApplication\Pricelist;
use JetApplication\Pricelists;
use JetApplication\WarehouseManagement_Warehouse;


/**
 *
 */
class Installer_Step_Install_Controller extends Installer_Step_Controller
{
	protected string $icon = 'gears';
	
	protected string $label = 'Install';
	
	protected string $error_message = '';
	
	public function main(): void
	{
		$this->catchContinue();
		
		$steps = [
			'createDb' => Tr::_('Create database'),
			'modules'  => Tr::_('Install modules'),
			'bases'    => Tr::_('Create bases'),
			'config'   => Tr::_('Saving configuration'),
			'createTemplate'   => Tr::_('Creating default template'),
			'pages'    => Tr::_('Create pages'),
			'sampleContent' => Tr::_('Installing example content'),
		];
		
		$installation_step = Http_Request::GET()->getString('is', default_value: '', valid_values: array_keys( $steps ));
		
		if($installation_step) {
			$method = 'install_'.$installation_step;
			
			$res = $this->{$method}();
			if($res) {
				AJAX::commonResponse([
					'ok' => true
				]);
			} else {
				AJAX::commonResponse([
					'ok' => false,
					'error' => $this->error_message
				]);
				
			}
		}
		
		$this->view->setVar('steps', $steps);
		
		
		$this->render('default');
	}
	
	public function install_createDb() : bool
	{
		
		/** @noinspection PhpFullyQualifiedNameUsageInspection */
		/** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
		$classes = [
			\JetApplication\AdministratorSignatures::class,
			\JetApplication\Auth_Administrator_Role::class,
			\JetApplication\Auth_Administrator_Role_Privilege::class,
			\JetApplication\Auth_Administrator_User::class,
			\JetApplication\Auth_Administrator_User_Roles::class,
			\JetApplication\Auth_RESTClient_Role::class,
			\JetApplication\Auth_RESTClient_Role_Privilege::class,
			\JetApplication\Auth_RESTClient_User::class,
			\JetApplication\Auth_RESTClient_User_Roles::class,
			\JetApplication\Auth_Visitor_Role::class,
			\JetApplication\Auth_Visitor_Role_Privilege::class,
			\JetApplication\Auth_Visitor_User::class,
			\JetApplication\Auth_Visitor_User_Roles::class,
			\JetApplication\Brand::class,
			\JetApplication\Brand_EShopData::class,
			\JetApplication\Carrier_DeliveryPoint::class,
			\JetApplication\Carrier_DeliveryPoint_OpeningHours::class,
			\JetApplication\Carrier_Packaging::class,
			\JetApplication\Carrier_Service::class,
			\JetApplication\Category::class,
			\JetApplication\Category_EShopData::class,
			\JetApplication\Category_Product::class,
			\JetApplication\CompanyInfo::class,
			\JetApplication\Complaint::class,
			\JetApplication\Complaint_ChangeHistory::class,
			\JetApplication\Complaint_ChangeHistory_Item::class,
			\JetApplication\Complaint_Event::class,
			\JetApplication\Complaint_Image::class,
			\JetApplication\Complaint_Note::class,
			\JetApplication\Content_Article::class,
			\JetApplication\Content_Article_Author::class,
			\JetApplication\Content_Article_Author_EShopData::class,
			\JetApplication\Content_Article_Category::class,
			\JetApplication\Content_Article_EShopData::class,
			\JetApplication\Content_Article_KindOfArticle::class,
			\JetApplication\Content_InfoBox::class,
			\JetApplication\Content_InfoBox_EShopData::class,
			\JetApplication\Content_InfoPage::class,
			\JetApplication\Content_InfoPage_EShopData::class,
			\JetApplication\Customer::class,
			\JetApplication\CustomerBlacklist::class,
			\JetApplication\Customer_Address::class,
			\JetApplication\DeliveryNote::class,
			\JetApplication\DeliveryNote_Item::class,
			\JetApplication\DeliveryNote_Item_SetItem::class,
			\JetApplication\Delivery_Class::class,
			\JetApplication\Delivery_Class_Method::class,
			\JetApplication\Delivery_Method::class,
			\JetApplication\Delivery_Method_Class::class,
			\JetApplication\Delivery_Method_EShopData::class,
			\JetApplication\Delivery_Method_PaymentMethods::class,
			\JetApplication\Delivery_Method_Price::class,
			\JetApplication\Discounts_Code::class,
			\JetApplication\Discounts_Code_Usage::class,
			\JetApplication\EMailMarketing_Subscribe::class,
			\JetApplication\EMailMarketing_Subscribe_Log::class,
			\JetApplication\EMail_Layout::class,
			\JetApplication\EMail_Layout_EShopData::class,
			\JetApplication\EMail_Sent::class,
			\JetApplication\EMail_TemplateText::class,
			\JetApplication\EMail_TemplateText_EShopData::class,
			\JetApplication\EShop_CookieSettings_Evidence_Agree::class,
			\JetApplication\EShop_CookieSettings_Evidence_Disagree::class,
			\JetApplication\Exports_ExportCategory::class,
			\JetApplication\Exports_ExportCategory_Parameter::class,
			\JetApplication\Exports_ExportCategory_Parameter_Value::class,
			\JetApplication\Exports_Join_Cache::class,
			\JetApplication\Exports_Join_KindOfProduct::class,
			\JetApplication\Exports_Join_Product::class,
			\JetApplication\Exports_Join_ProductCommonData::class,
			\JetApplication\Exports_PlannedOutage::class,
			\JetApplication\FulltextSearch_Dictionary::class,
			\JetApplication\Invoice::class,
			\JetApplication\InvoiceInAdvance::class,
			\JetApplication\InvoiceInAdvance_Item::class,
			\JetApplication\InvoiceInAdvance_Item_SetItem::class,
			\JetApplication\Invoice_Item::class,
			\JetApplication\Invoice_Item_SetItem::class,
			\JetApplication\KindOfProduct::class,
			\JetApplication\KindOfProduct_EShopData::class,
			\JetApplication\KindOfProduct_Property::class,
			\JetApplication\KindOfProduct_PropertyGroup::class,
			\JetApplication\Logger_Admin_Event::class,
			\JetApplication\Logger_EShop_Event::class,
			\JetApplication\Logger_Exports_Event::class,
			\JetApplication\Logger_REST_Event::class,
			\JetApplication\Logger_SysServices_Event::class,
			\JetApplication\Marketing_AutoOffer::class,
			\JetApplication\Marketing_Banner::class,
			\JetApplication\Marketing_BannerGroup::class,
			\JetApplication\Marketing_DeliveryFeeDiscount::class,
			\JetApplication\Marketing_Gift_Product::class,
			\JetApplication\Marketing_Gift_ShoppingCart::class,
			\JetApplication\Marketing_LandingPage::class,
			\JetApplication\Marketing_ProductSticker::class,
			\JetApplication\Marketing_PromoArea::class,
			\JetApplication\Marketing_PromoAreaDefinition::class,
			\JetApplication\MarketplaceIntegration_Join_Brand::class,
			\JetApplication\MarketplaceIntegration_Join_Cache::class,
			\JetApplication\MarketplaceIntegration_Join_KindOfProduct::class,
			\JetApplication\MarketplaceIntegration_Join_Product::class,
			\JetApplication\MarketplaceIntegration_Join_ProductCommonData::class,
			\JetApplication\MarketplaceIntegration_MarketplaceBrand::class,
			\JetApplication\MarketplaceIntegration_MarketplaceCategory::class,
			\JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter::class,
			\JetApplication\MarketplaceIntegration_MarketplaceCategory_Parameter_Value::class,
			\JetApplication\NumberSeries_Counter_Day::class,
			\JetApplication\NumberSeries_Counter_Month::class,
			\JetApplication\NumberSeries_Counter_Total::class,
			\JetApplication\NumberSeries_Counter_Year::class,
			\JetApplication\Order::class,
			\JetApplication\OrderDispatch::class,
			\JetApplication\OrderDispatch_Event::class,
			\JetApplication\OrderDispatch_Item::class,
			\JetApplication\OrderDispatch_Packet::class,
			\JetApplication\OrderDispatch_TrackingHistory::class,
			\JetApplication\Order_ChangeHistory::class,
			\JetApplication\Order_ChangeHistory_Item::class,
			\JetApplication\Order_Event::class,
			\JetApplication\Order_Item::class,
			\JetApplication\Order_Item_SetItem::class,
			\JetApplication\Order_Note::class,
			\JetApplication\Payment_Method::class,
			\JetApplication\Payment_Method_DeliveryMethods::class,
			\JetApplication\Payment_Method_EShopData::class,
			\JetApplication\Payment_Method_Option::class,
			\JetApplication\Payment_Method_Option_EShopData::class,
			\JetApplication\Payment_Method_Price::class,
			\JetApplication\Product::class,
			\JetApplication\ProductFilter_Storage::class,
			\JetApplication\ProductFilter_Storage_Value::class,
			\JetApplication\ProductQuestion::class,
			\JetApplication\ProductReview::class,
			\JetApplication\Product_Availability::class,
			\JetApplication\Product_Box::class,
			\JetApplication\Product_EShopData::class,
			\JetApplication\Product_File::class,
			\JetApplication\Product_Image::class,
			\JetApplication\Product_KindOfFile::class,
			\JetApplication\Product_KindOfFile_EShopData::class,
			\JetApplication\Product_Parameter_InfoNotAvl::class,
			\JetApplication\Product_Parameter_TextValue::class,
			\JetApplication\Product_Parameter_Value::class,
			\JetApplication\Product_Price::class,
			\JetApplication\Product_PriceHistory::class,
			\JetApplication\Product_Relation::class,
			\JetApplication\Product_SetItem::class,
			\JetApplication\Product_Similar::class,
			\JetApplication\Property::class,
			\JetApplication\PropertyGroup::class,
			\JetApplication\PropertyGroup_EShopData::class,
			\JetApplication\Property_EShopData::class,
			\JetApplication\Property_Options_Option::class,
			\JetApplication\Property_Options_Option_EShopData::class,
			\JetApplication\ReturnOfGoods::class,
			\JetApplication\ReturnOfGoods_ChangeHistory::class,
			\JetApplication\ReturnOfGoods_ChangeHistory_Item::class,
			\JetApplication\ReturnOfGoods_Event::class,
			\JetApplication\ReturnOfGoods_Note::class,
			\JetApplication\Signpost::class,
			\JetApplication\Signpost_Category::class,
			\JetApplication\Signpost_EShopData::class,
			\JetApplication\Supplier::class,
			\JetApplication\Supplier_GoodsOrder::class,
			\JetApplication\Supplier_GoodsOrder_Item::class,
			\JetApplication\SysServices_PlannedOutage::class,
			\JetApplication\Timer::class,
			\JetApplication\WarehouseManagement_LossOrDestruction::class,
			\JetApplication\WarehouseManagement_ReceiptOfGoods::class,
			\JetApplication\WarehouseManagement_ReceiptOfGoods_Item::class,
			\JetApplication\WarehouseManagement_StockCard::class,
			\JetApplication\WarehouseManagement_StockMovement::class,
			\JetApplication\WarehouseManagement_StockVerification::class,
			\JetApplication\WarehouseManagement_StockVerification_Item::class,
			\JetApplication\WarehouseManagement_TransferBetweenWarehouses::class,
			\JetApplication\WarehouseManagement_TransferBetweenWarehouses_Item::class,
			\JetApplication\WarehouseManagement_Warehouse::class,
			\JetApplication\Statistics_Product_ViewLog::class,
			\JetApplication\Statistics_Product_OrderLog::class,
			\JetApplication\Statistics_Category_ViewLog::class,
			\JetApplication\Statistics_Order_SourceLog::class,
		];
		
		$result = [];
		$OK = true;
		
		foreach( $classes as $class ) {
			$result[$class] = true;
			try {
				DataModel_Helper::create( $class );
			} catch( Error|Exception $e ) {
				$result[$class] = $e->getMessage();
				$OK = false;
			}
			
		}
		
		
		if(!$OK) {
			$this->view->setVar( 'result', $result );
			$this->view->setVar( 'OK', false );
			
			$this->error_message = $this->view->render( 'create-db' );
		}
		
		return $OK;
	}
	
	public function install_modules() : bool
	{
		$all_modules = Application_Modules::allModulesList();
		$modules_scope = [];
		$selected_modules = [];
		foreach($all_modules as $module) {
			$modules_scope[$module->getName()] = $module->getLabel();
			$selected_modules[] = $module->getName();
		}
		
		
		$this->view->setVar( 'modules', $all_modules );
		
		
		$this->catchContinue();
		
		
		
		
		$result = [];
		
		$OK = true;
		
		$tr_dir = SysConf_Path::getDictionaries();
		SysConf_Path::setDictionaries(__APP_DICTIONARIES__);
		
		foreach( $selected_modules as $module_name ) {
			$result[$module_name] = true;
			
			if( $all_modules[$module_name]->isActivated() ) {
				continue;
			}
			
			try {
				Application_Modules::installModule( $module_name );
			} catch( Error|Exception $e ) {
				$result[$module_name] = $e->getMessage();
				
				$OK = false;
			}
			
			if( $result[$module_name] !== true ) {
				continue;
			}
			
			try {
				Application_Modules::activateModule( $module_name );
			} catch( Error|Exception $e ) {
				$result[$module_name] = $e->getMessage();
				$OK = false;
			}
			
		}
		
		SysConf_Path::setDictionaries( $tr_dir );
		

		if(!$OK) {
			$this->view->setVar( 'result', $result );
			$this->view->setVar( 'OK', false );
			
			$this->error_message = $this->view->render( 'modules-installation-result' );
		}
		
		return true;
		
		
	}
	
	public function install_bases() : bool
	{
		$bases = Installer::getBases();
		
		try {
			foreach( $bases as $base ) {
				$base->saveDataFile();
			}
			
		} catch( Error|Exception $e ) {
			$this->error_message = UI_messages::createDanger(
				Tr::_( 'Something went wrong: %error%', ['error' => $e->getMessage()], Translator::COMMON_DICTIONARY )
			);
			
			return false;
		}
		
		return true;
	}
	
	public function install_pages() : bool
	{
		foreach(Installer::getSelectedEshopLocales() as $locale) {
			EShop_Pages::createPages( EShops::get( EShop::generateKey('default', $locale) ) );
		}
		
		return true;
	}
	
	
	public function install_config() : bool
	{
		try {
			$this->install_config_pricelists();
			$this->install_config_availabilities();
			$this->install_config_eshops();
			
		} catch( Error|Exception $e ) {
			$this->error_message = UI_messages::createDanger(
				Tr::_( 'Something went wrong: %error%', ['error' => $e->getMessage()], Translator::COMMON_DICTIONARY )
			);
			
			return false;
		}
		
		
		return true;
	}
	
	public function install_config_pricelists() : void
	{
		foreach(Installer::getSelectedEshopLocales() as $locale) {
			$code = 'default_'.$locale->toString();
			if(!Pricelists::exists( $code )) {
				
				$currency_code = Installer::getCurrencies()[$locale->toString()];
				$currency = Currencies::get( $currency_code );
				$vat_rates = Installer::getVATRates( $locale );
				
				$pl = new Pricelist();
				$pl->setCode( $code );
				$pl->setName( $locale->getRegionName().' - '.$currency->getCode() );
				$pl->setCurrencyCode( $currency->getCode() );
				$pl->setVatRates( $vat_rates );
				$pl->setDefaultVatRate( $vat_rates[0] );
				
				Pricelists::addPricelist( $pl );
			}
		}
		
		Pricelists::saveCfg();
	}
	
	public function install_config_availabilities() : void
	{
		switch(Installer::getAvailabilityStrategy()) {
			case 'global':
				$wh_code = 'default';
				if(!($wh=WarehouseManagement_Warehouse::getByInternalCode($wh_code))) {
					$wh = new WarehouseManagement_Warehouse();
					$wh->setInternalName( $wh_code );
					$wh->setInternalCode( $wh_code );
					$wh->save();
				}
				
				$code = 'default';
				if(!Availabilities::exists($code)) {
					$avl = new Availability();
					$avl->setCode( $code );
					$avl->setName( 'default' );
					$avl->setWarehouseIds( [$wh->getId()] );
					
					Availabilities::addAvailability( $avl );
				}
				break;
			case 'by_locale':
				
				foreach(Installer::getSelectedEshopLocales() as $locale) {
					$wh_code = 'default_'.$locale;
					if(!($wh=WarehouseManagement_Warehouse::getByInternalCode($wh_code))) {
						$wh = new WarehouseManagement_Warehouse();
						$wh->setInternalName( $locale->getRegionName() );
						$wh->setInternalCode( $wh_code );
						$wh->save();
					}
					
					$code = 'default_'.$locale;
					if(!Availabilities::exists($code)) {
						$avl = new Availability();
						$avl->setCode( $code );
						$avl->setName( $locale->getRegionName() );
						$avl->setWarehouseIds( [$wh->getId()] );
						
						Availabilities::addAvailability( $avl );
					}
					
				}
				
				break;
		}
		
		Availabilities::saveCfg();
	}
	
	public function install_config_eshops() : void
	{
		$code = 'default';
		
		$base = Application_EShop::getBase();
		$has_default = false;
		foreach(Installer::getSelectedEshopLocales() as $locale) {
			
			$key = EShop::generateKey( $code, $locale );
			if(!EShops::exists( $key )) {
				$eshop = new EShop();
				$eshop->setCode( $code );
				$eshop->setLocale( $locale );
				$eshop->setBaseId( $base->getId() );
				$eshop->setName( $locale->getRegionName() );
			} else {
				$eshop = EShops::get( $key );
			}
			
			$eshop->setIsActive( true );
			$eshop->setURLs( $base->getLocalizedData( $locale )->getURLs() );
			
			if(!$has_default) {
				$eshop->setIsDefault( true );
				$has_default = true;
			} else {
				$eshop->setIsDefault( false );
			}
			
			$pricelist_code = 'default_'.$locale->toString();
			$eshop->setPricelistCodes([$pricelist_code]);
			$eshop->setDefaultPricelistCode( $pricelist_code );
			
			
			$wh_code = 'default';
			$avl_code = 'default';
			
			if(Installer::getAvailabilityStrategy()=='by_locale') {
				$wh_code = 'default_'.$locale;
				$avl_code = 'default_'.$locale;
			}
			
			$wh=WarehouseManagement_Warehouse::getByInternalCode($wh_code);
			$eshop->setAvailabilityCodes( [$avl_code] );
			$eshop->setDefaultAvailabilityCode( $avl_code );
			$eshop->setDefaultWarehouseId( $wh->getId() );
			
			$eshop->setUseTemplate( true );
			$eshop->setTemplateRelativeDir( 'default' );
			
			
			$eshop->save();
			
			
		}
	}
	
	public function install_createTemplate() : bool
	{
		$template = new EShop_Template('default');
		
		$template->createFromDevelopmentScripts();
		
		return true;
	}
	
	public function install_sampleContent() : bool
	{
		//TODO:
		return true;
	}
}