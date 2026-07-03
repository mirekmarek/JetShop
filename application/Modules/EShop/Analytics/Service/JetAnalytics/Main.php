<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Analytics\Service\JetAnalytics;

use Jet\Tr;
use JetApplication\Admin_EntityManager_EditTabProvider;
use JetApplication\CashDesk;
use JetApplication\Category_EShopData;
use JetApplication\EShop;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\Application_Service_EShop_AnalyticsService;
use JetApplication\ProductListing;
use JetApplication\ShoppingCart;
use JetApplication\ShoppingCart_Item;
use JetApplication\Signpost_EShopData;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Service_EShop_AnalyticsService implements Admin_EntityManager_EditTabProvider, SysServices_Provider_Interface
{
	use Main_Trait_Admin;
	
	protected bool $test_mode = false;
	
	protected null|false|Session $session = null;
	
	public function allowed() : bool
	{
		return true;
	}
	
	public function init( EShop $eshop ) : void
	{
		parent::init( $eshop );
		$this->enabled = true;
	}
	
	public function initTest( EShop $eshop ) : void
	{
		parent::init( $eshop );
		$this->testing_allowed = false;
		$this->test_mode = true;
	}
	
	protected function initSession() : void
	{
		if($this->session===null) {
			$this->session = Session::getCurrent();
		}
	}
	
	public function header(): string
	{
		$this->initSession();
		return '';
	}
	
	
	public function documentStart(): string
	{
		$this->initSession();
		return '';
	}
	
	public function documentEnd(): string
	{
		$this->initSession();
		return '';
	}
	
	public function viewHomePage() : string
	{
		return '';
	}
	
	public function viewCategory( Category_EShopData $category, ?ProductListing $product_listing = null ): string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CategoryView::create();
			$event->init( $category, $product_listing );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	public function viewSignpost( Signpost_EShopData $signpost ): string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_SignpostView::create();
			$event->init( $signpost );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	
	public function viewProductDetail( Product_EShopData $product ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_ProductDetailView::create();
			$event->init( $product );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	
	public function addToCart( ShoppingCart_Item $new_cart_item ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_AddToCart::create();
			$event->init( $new_cart_item );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	public function removeFromCart( ShoppingCart_Item $cart_item ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_RemoveFromCart::create();
			$event->init( $cart_item );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	public function viewCart( ShoppingCart $cart ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CartView::create();
			$event->init( $cart );
			$this->session->addEvent( $event );
		}
		
		return '';
	}
	
	
	public function beginCheckout( CashDesk $cash_desk ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CheckoutStarted::create();
			$event->init( $cash_desk->getOrder() );
			$this->session->addEvent( $event );
		}
		return '';
	}
	
	public function checkoutInProgress( CashDesk $cash_desk ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_CheckoutInProgress::create();
			$event->init( $cash_desk->getOrder() );
			$this->session->addEvent( $event );
		}
		return '';
	}

	
	public function purchase( Order $order ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_Purchase::create();
			$event->init( $order );
			$this->session->addEvent( $event );
		}
		return '';
	}
	
	public function searchWhisperer( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_SearchWhisperer::create();
			$event->init( $q, $result_ids );
			$this->session->addEvent( $event );
		}

		return '';
	}
	
	public function search( string $q, array $result_ids, ?ProductListing $product_listing = null ) : string
	{
		$this->initSession();
		if($this->session) {
			$event = Event_Search::create();
			$event->init( $q, $result_ids, $product_listing );
			$this->session->addEvent( $event );
		}
		return '';
	}
	
	public function customEvent( string $event, array $event_data = [] ): string
	{
		$this->initSession();
		if($this->session) {
			$e = Event_Custom::create();
			$e->init( $event, $event_data );
			$this->session->addEvent( $e );
		}
		return '';
	}
	
	public function getSysServicesDefinitions(): array
	{
		$cleanup = new SysServices_Definition(
			module: $this,
			name: Tr::_('Jet Analytics - Cleanup'),
			description: '',
			service_code: 'ja_cleanup',
			service: function() {
				Session::cleanup();
			}
		);
		
		$determine_source = new SysServices_Definition(
			module: $this,
			name: Tr::_('Jet Analytics - determine session source'),
			description: '',
			service_code: 'ja_determine_source',
			service: function() {
				Session::determineSources();
			}
		);
		$determine_source->setIsPeriodicallyTriggeredService( false );
		
		
		$repair_session = new SysServices_Definition(
			module: $this,
			name: 'Jet Analytics - Repair session',
			description: '',
			service_code: 'ja_repair',
			service: function() {
				$JaSession = new class extends Session{
					
					public static function getIds() : array
					{
						return static::dataFetchCol(
							select: ['id'],
							where: ['session_duration' => 0],
							order_by: ['-id'],
							limit: 5000,
						);
					}
					
					public function repair() : void
					{
						$this->session_duration = ($this->last_activity_date_time?->getTimestamp()??0) - ($this->start_date_time?->getTimestamp()??0);
						
						/*
						$event_map = $this->getEventMap();
						
						foreach($event_map as $evm_item) {
							switch($evm_item->getEventType()) {
								case 'CheckoutStarted':
								case 'CheckoutInProgress':
									$this->checkout_started = true;
									break;
								case 'Purchase':
									$this->purchased = true;
									break;
							}
						}
						
						$this->event_counter = count($event_map);
						*/
						
						$this->save();
					}
				};
				
				$ids = $JaSession::getIds();
				
				$count = count( $ids );
				$c = 0;
				
				foreach($ids as $id) {
					$c++;
					
					echo "[{$c}/{$count}] {$id}\n";
					
					$s = $JaSession::load( $id );
					$s->repair();
				}

			}
		);
		
		$repair_session->setIsPeriodicallyTriggeredService( false );
		
		return [
			$cleanup,
			$determine_source,
			//$repair_session
		];
	}
}