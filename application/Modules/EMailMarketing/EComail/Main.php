<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\EComail;


use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Admin_ControlCentre;
use JetApplication\Admin_ControlCentre_Module_Interface;
use JetApplication\Admin_ControlCentre_Module_Trait;
use JetApplication\Application_Service_EShop;
use JetApplication\Application_Service_EShop_EMailMarketingSubscribeManagerBackend;
use JetApplication\Customer;
use JetApplication\EShop;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface;
use JetApplication\EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
use JetApplication\EShops;
use JetApplication\ShoppingCart_Item;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;

class Main extends Application_Service_EShop_EMailMarketingSubscribeManagerBackend implements
	Admin_ControlCentre_Module_Interface,
	EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Interface,
	SysServices_Provider_Interface
{
	use Admin_ControlCentre_Module_Trait;
	use EShopConfig_ModuleConfig_ModuleHasConfig_PerShop_Trait;
	
	protected function getCfg(  EShop $eshop ) : Config_PerShop
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return $this->getEshopConfig( $eshop );
	}
	
	public function getEcomail( EShop $eshop ) : Ecomail
	{
		return new Ecomail( $this->getCfg( $eshop ) );
	}
	
	public function isSubscribed( EShop $eshop, string $email_address ): bool
	{
		$info = $this->getEcomail( $eshop )->getSubscriberInfo( $email_address );
		if(!$info) {
			return false;
		}
		
		$status = $info['status']??null;
		
		return $status==1;
	}
	
	public function subscribe( EShop $eshop, string $email_address, string $source, string $comment = '' ): void
	{
		$this->getEcomail( $eshop )->subscribe( $email_address );
	}
	
	public function unsubscribe( EShop $eshop, string $email_address, string $source, string $comment = '' ): void
	{
		$this->getEcomail( $eshop )->unsubscribe( $email_address );
	}
	
	public function changeMail( EShop $eshop, string $old_email_address, string $new_mail_address, string $source, string $comment = '' ): void
	{
		$this->getEcomail( $eshop )->changeMail( $old_email_address, $new_mail_address );
	}
	
	public function delete( EShop $eshop, string $email_address, string $source, string $comment = '' ): void
	{
		$this->getEcomail( $eshop )->delete( $email_address );
	}
	
	
	public function showStatus( EShop $eshop, string $email ): string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() : string {
				return '';
			}
		);
	}
	
	public function getControlCentreGroup(): string
	{
		return Admin_ControlCentre::GROUP_MAIN;
	}
	
	public function getControlCentreTitle(): string
	{
		return 'EComail mailing';
	}
	
	public function getControlCentreIcon(): string
	{
		return 'envelope';
	}
	
	public function getControlCentrePriority(): int
	{
		return 99;
	}
	
	public function getControlCentrePerShopMode(): bool
	{
		return true;
	}
	
	protected bool $store_handler_set = false;
	
	public function storeCart() : void
	{
		if( $this->store_handler_set ) {
			return;
		}
		
		$this->store_handler_set = true;
		
		register_shutdown_function(function() {
			$customer = Customer::getCurrentCustomer();
			if(
				!$customer ||
				!$customer->getMailingSubscribed()
			) {
				return;
			}
			
			$cart = Application_Service_EShop::ShoppingCart()->getCart();
			
			$items = $cart->getItems();
			
			$rec = new CartToBeSend();
			
			$rec->setCartId( $cart->getId() );
			$rec->setEshopKey( $cart->getEshop()->getKey() );
			$rec->setCustomerId( $customer->getId() );
			$rec->setInsertedDateTime( Data_DateTime::now() );
			$rec->setItems( base64_encode(serialize( $items )) );
			$rec->setItemsCount( count( $items ) );
			
			$rec->save();
			
		});
	}
	
	public function sendCartTracking( EShop $eshop ) : void
	{
		$ttl = Data_DateTime::catchDateTime( date('Y-m-d H:i:s', strtotime('-2 months')) );
		
		CartToBeSend::dataDelete(where: [
			'eshop_key' => $eshop->getKey(),
			'AND',
			'inserted_date_time <' => $ttl,
		]);

		
		$not_processed = CartToBeSend::fetch(
			[''=>[
				'eshop_key' => $eshop->getKey(),
				'AND',
				'processed' => false
			]],
			order_by: 'id'
		);
		
		foreach($not_processed as $rec){
			$customer_id = $rec->getCustomerId();
			/**
			 * @var ShoppingCart_Item[] $items
			 */
			$items = unserialize( base64_decode( $rec->getItems() ) );
			
			$customer = Customer::get( $customer_id );
			if(!$customer) {
				$rec->processed( 'Zákaznický účet již neexistuje' );
				continue;
			}
			
			if(!$customer->getMailingSubscribed()) {
				$rec->processed( 'Zákazník zrušil odběr novinek' );
				continue;
			}
			
			$products = [];
			foreach($items as $item) {
				
				$p = $item->getProduct();
				if(!$p) {
					continue;
				}
				
				$products[] = [
					'productId' => $item->getProductId(),
					'img_url' => $p->getImage(0)->getUrl(),
					'url' => $p->getUrl(),
					'name' => $p->getName(),
					'price' => $p->getPrice( $eshop->getDefaultPricelist() ),
					'description' => ''
				];
			}
			
			$event_data = ['data' => ['data' => [
				'action' => 'Basket',
				'products' => $products
			]]];
			
			$event_data = [
				'event' => [
					'email' => $customer->getEmail(),
					'category' => 'ue',
					'action' => 'Basket',
					'label' => 'Basket',
					'value' => json_encode( $event_data )
				]
			];
			
			$res = $this->getEcomail( $eshop )->addEvent(  $event_data);
			$res = json_encode( $res );
			
			$rec->processed( $res );
			
		}
		
	}
	
	public function getSysServicesDefinitions(): array
	{
		$services = [];
		foreach(EShops::getList() as $eshop) {
			/**
			 * @var Config_PerShop $config
			 */
			$config = $this->getEshopConfig( $eshop );
			if(
				!$config->getApiUrl() ||
				!$config->getApiKey()
			) {
				continue;
			}
			
			$send_carts = new SysServices_Definition(
				module:        $this,
				name:          Tr::_( 'EComail - send shopping cart tracking %ESHOP% ', ['ESHOP'=>$eshop->getName()] ),
				description:   Tr::_( 'Send shopping cart tracking to the EComail API' ),
				service_code: 'send_cart_tracking:'.$eshop->getKey(),
				service:       function() use ($eshop) {
					$this->sendCartTracking( $eshop );
				}
			
			);
			
			$send_carts->setIsPeriodicallyTriggeredService( true );
			$send_carts->setServiceRequiresEshopDesignation( false );
			
			$services[] = $send_carts;
		}
		
		return $services;
	}
	
}