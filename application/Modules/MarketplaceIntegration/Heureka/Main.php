<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */

/** @noinspection SpellCheckingInspection */
namespace JetApplicationModule\MarketplaceIntegration\Heureka;


use Jet\Logger;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Application_Service_EShop_NewOrderPostprocessor;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Event_Cancel;
use JetApplication\Order_Event_Delivered;
use JetApplication\Order_Event_Dispatched;
use JetApplication\Order_Event_Returned;
use JetApplication\OrderDispatch;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use JetApplication\EShops;
use JetApplication\EShop;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends MarketplaceIntegration_Module implements
	Admin_ControlCentre_Module_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	SysServices_Provider_Interface,
	Application_Service_EShop_NewOrderPostprocessor
{
	use Admin_ControlCentre_Module_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	
	public const IMPORT_SOURCE = 'Heureka';
	
	public function getTitle(): string
	{
		return 'Heureka';
	}
	
	public function hasProductSettings() : bool
	{
		return false;
	}
	
	public function hasKindOfProductSettings() : bool
	{
		return false;
	}
	
	public function hasBrandSettings() : bool
	{
		return false;
	}
	
	
	public function isAllowedForShop( EShop $eshop ): bool
	{
		return $this->getConfig( $eshop )->getApiUrl();
	}
	
	public function getConfig( EShop $eshop ) : Config_PerShop|EShopConfig_ModuleConfig_PerShop
	{
		return $this->getEshopConfig( $eshop );
	}
	
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MARKET_PLACE_INTEGRATION;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'Heureka';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'cloud-arrow-up';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
	public function actualizeBrands(): void
	{
	}
	
	public function actualizeCategories(): void
	{
	}
	
	public function actualizeCategory( string $category_id ): void
	{
	}
	
	public function getSysServicesDefinitions(): array
	{
		$servers = [];
		
		foreach( EShops::getList() as $eshop) {
			if(!$this->getConfig($eshop)->getApiUrl()) {
				continue;
			}
			
			$servers[$eshop->getKey()] = new SysServices_Definition(
				module: $this,
				name: Tr::_('Heureka Marketplace Server - '.$eshop->getName()),
				description: Tr::_('Heureka Marketplace Server'),
				service_code: 'mp_server_'.$eshop->getKey(),
				service: function() use ($eshop)  {
					$this->getServer( $eshop )->handle();
				}
			);
		}
		
		
		return $servers;
	}
	
	public function getServer( EShop $eshop ) : Server
	{
		return new Server( $this->getConfig( $eshop ) );
	}
	
	public function getClient( EShop $eshop ) : Client
	{
		return new Client( $this->getConfig( $eshop ) );
	}
	
	public function handleOrderEvent( Order_Event $order_event ): bool
	{
		return match ( $order_event->getEvent() ) {
			Order_Event_Cancel::getCode() => $this->handleEvent_cancel( $order_event ),
			Order_Event_Dispatched::getCode() => $this->handleEvent_dispatched( $order_event ),
			Order_Event_Delivered::getCode() => $this->handleEvent_delivered( $order_event ),
			Order_Event_Returned::getCode() => $this->handleEvent_returned( $order_event ),
			default => true,
		};

		return true;
	}
	
/*
0:  objednávka vyexpedována (obchod odeslal objednávku zákazníkovi)
1:  objednávka odeslána do obchodu
2:  objednávka byla vyřízena jen částečně (počítá se s tím, že bude v budoucnu doručena kompletní)
3:  objednávka potvrzena (obchod objednávku přijal a potvrzuje, že ji začíná zpracovávat)
4:  storno z pohledu obchodu (obchod stornoval objednávku)
5:  storno z pohledu zákazníka (zákazník se rozhodl stornovat objednávku)
6:  storno - objednávka nebyla zaplacena (zákazník nezaplatil za objednávku)
7:  vráceno ve 14 denní lhůtě (zákazník vrátil zboží v zákonné 14 denní lhůtě)
8:  objednávka byla dokončena na Heurece (objednávka byla správně dokončena na Heurece)
9:  objednávka dokončena (zákazník zaplatil a převzal objednávku)
10: objednávka připravena k vyzvednutí (objednávka je připravena pro osobní odběr na pobočce)
11: vyexpedováno na externí výdejní místo (např. Heureka point)

 */
	
	protected function handleEvent_cancel( Order_Event $order_event ) : bool
	{
		//4:  storno z pohledu obchodu (obchod stornoval objednávku)
		return $this->setStatus( $order_event, 4 );
	}
	
	protected function handleEvent_dispatched( Order_Event $order_event ) : bool
	{
		$client = $this->getClient( $order_event->getEshop() );
		
		$tracking_url = '';
		$dispatch = $order_event->getContext();
		if($dispatch instanceof OrderDispatch) {
			$tracking_url = $dispatch->getTrackingURL();
		}
		
		if(!$client->putHeurekaOrderStatus(
			$order_event->getOrder()->getNumber(),
			0,
			tracking_url: $tracking_url
		)) {
			$order_event->setErrorMessage( $client->getLastErrorMessage() );
			
			return false;
		}
		return true;
	}
	
	protected function handleEvent_delivered( Order_Event $order_event ) : bool
	{
		//9:  objednávka dokončena (zákazník zaplatil a převzal objednávku)
		return $this->setStatus( $order_event, 9 );
	}
	
	protected function handleEvent_returned( Order_Event $order_event ) : bool
	{
		return true;
	}
	
	protected function setStatus( Order_Event $order_event, int $heureka_status_id) : bool
	{
		$client = $this->getClient( $order_event->getEshop() );
		if(!$client->putHeurekaOrderStatus(
			$order_event->getOrder()->getNumber(),
			$heureka_status_id
		)) {
			$order_event->setErrorMessage( $client->getLastErrorMessage().var_export($client->getResponseData(), true) );
			
			return false;
		}
		return true;
	}
	
	public function processNewOrder( Order $order ) : void
	{
		if($order->isSurveyDisagreement()) {
			return;
		}
		
		/**
		 * @var Config_PerShop $config
		 */
		$config = $this->getEshopConfig( $order->getEshop() );
		
		if(
			!$config->getOverenoAPIURL() ||
			!$config->getOverenoAPIKey()
		) {
			return;
		}
		
		if(
			!in_array(
				$order->getImportSource(),
				['', static::IMPORT_SOURCE]
			)
		) {
			return;
		}
		
		$overeno = new HeurekaOvereno( $config );
		try {
			$overeno->setOrderId( $order->getNumber() );
			$overeno->setEmail( $order->getEmail() );
			
			foreach($order->getItems() as $item) {
				if(
					$item->isPhysicalProduct() ||
					$item->isVirtualProduct()
				) {
					$overeno->addProduct( $item->getItemId(), $item->getTitle() );
				}
			}
			
			$overeno->send();
		} catch( HeurekaOvereno_Exception $e ) {
			Logger::danger('HeurekaOvereno-send:failed', $e->getMessage());
		}
		
	}
}