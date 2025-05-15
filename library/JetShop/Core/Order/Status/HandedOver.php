<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Event_PersonalReceiptHandedOver;
use JetApplication\Order_Status;

abstract class Core_Order_Status_HandedOver extends Order_Status {
	
	public const CODE = 'handed_over';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'returned' => false,
		
		'delivered' => true,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => null,
		'dispatch_started' => null,
		'dispatched' => null,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Handed over', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 60;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( Order|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ) : Order_Event
	{
		return $item->createEvent( Order_Event_PersonalReceiptHandedOver::new() );
	}
	
	public static function resolve( EShopEntity_HasStatus_Interface|Order $item ) : bool
	{
		if(!$item->getDeliveryMethod()?->isPersonalTakeover()) {
			return false;
		}
		
		return parent::resolve( $item );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}