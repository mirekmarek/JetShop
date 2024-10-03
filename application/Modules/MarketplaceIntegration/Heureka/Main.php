<?php /** @noinspection SpellCheckingInspection */

/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\MarketplaceIntegration\Heureka;

use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\MarketplaceIntegration_Module;
use JetApplication\Order_Event;
use JetApplication\OrderDispatch;
use JetApplication\ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\ShopConfig_ModuleConfig_PerShop;
use JetApplication\Shops;
use JetApplication\Shops_Shop;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;
use JetApplicationModule\Admin\Orders\Order;
use JetShop\Core_ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;


class Main extends MarketplaceIntegration_Module implements
	Admin_ControlCentre_Module_Interface,
	ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	SysServices_Provider_Interface
{
	use Admin_ControlCentre_Module_Trait;
	use Core_ShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	
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
	
	
	public function isAllowedForShop( Shops_Shop $shop ): bool
	{
		return $this->getConfig( $shop )->getApiUrl();
	}
	
	public function getConfig( Shops_Shop $shop ) : Config_PerShop|ShopConfig_ModuleConfig_PerShop
	{
		return $this->getShopConfig( $shop );
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
	
	public function actualizeBrands( Shops_Shop $shop ): void
	{
	}
	
	public function actualizeCategories( Shops_Shop $shop ): void
	{
	}
	
	public function actualizeCategory( Shops_Shop $shop, string $category_id ): void
	{
	}
	
	public function getSysServicesDefinitions(): array
	{
		$servers = [];
		
		foreach(Shops::getList() as $shop) {
			if(!$this->getConfig($shop)->getApiUrl()) {
				continue;
			}
			
			$servers[$shop->getKey()] = new SysServices_Definition(
				module: $this,
				name: Tr::_('Heureka Marketplace Server - '.$shop->getShopName()),
				description: Tr::_('Heureka Marketplace Server'),
				service_code: 'mp_server_'.$shop->getKey(),
				service: function() use ($shop)  {
					$this->getServer( $shop )->handle();
				}
			);
		}
		
		
		return $servers;
	}
	
	public function getServer( Shops_Shop $shop ) : Server
	{
		return new Server( $this->getConfig( $shop ) );
	}
	
	public function getClient( Shops_Shop $shop ) : Client
	{
		return new Client( $this->getConfig( $shop ) );
	}
	
	public function handleOrderEvent( Order_Event $order_event ): bool
	{
		return match ($order_event->getEvent()) {
			Order::EVENT_CANCEL => $this->handleEvent_cancel( $order_event ),
			Order::EVENT_DISPATCHED => $this->handleEvent_dispatched( $order_event ),
			Order::EVENT_DELIVERED => $this->handleEvent_delivered( $order_event ),
			Order::EVENT_RETURNED => $this->handleEvent_returned( $order_event ),
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
		$client = $this->getClient( $order_event->getShop() );
		
		$tracking_url = '';
		$dispatch = $order_event->getContext();
		if($dispatch instanceof OrderDispatch) {
			$tracking_url = $dispatch->getTrackingURL();
		}
		
		if(!$client->putHeurekaOrderStatus(
			$order_event->getOrderId(),
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
		$client = $this->getClient( $order_event->getShop() );
		if(!$client->putHeurekaOrderStatus(
			$order_event->getOrderId(),
			$heureka_status_id
		)) {
			$order_event->setErrorMessage( $client->getLastErrorMessage() );
			
			return false;
		}
		return true;
	}

}