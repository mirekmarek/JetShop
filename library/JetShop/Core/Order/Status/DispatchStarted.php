<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Event_DispatchStarted;
use JetApplication\Order_Status;
use JetApplication\Order_Status_Cancelled;

abstract class Core_Order_Status_DispatchStarted extends Order_Status {
	
	public const CODE = 'dispatch_started';
	protected string $title = 'Dispatch started';
	protected int $priority = 40;
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'delivered' => false,
		'returned' => false,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => true,
		'dispatch_started' => true,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-processing';
	}
	
	public function createEvent( Order|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ) : Order_Event
	{
		return $item->createEvent( Order_Event_DispatchStarted::new() );
	}
	
	public static function resolve( EShopEntity_HasStatus_Interface|Order $item ) : bool
	{
		if($item->getDeliveryMethod()?->isPersonalTakeover()) {
			return false;
		}
		
		return parent::resolve( $item );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = Order_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}