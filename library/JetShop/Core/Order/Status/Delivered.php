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
use JetApplication\Order_Event_Delivered;
use JetApplication\Order_Status;
use JetApplication\Order_Status_Cancelled;

abstract class Core_Order_Status_Delivered extends Order_Status {
	
	public const CODE = 'delivered';
	protected string $title = 'Delivered';
	protected int $priority = 60;
	
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
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( Order|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ) : Order_Event
	{
		return $item->createEvent( Order_Event_Delivered::new() );
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