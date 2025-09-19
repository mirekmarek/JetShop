<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Event_Cancel;
use JetApplication\Order_Status;

abstract class Core_Order_Status_Cancelled extends Order_Status {
	
	public const CODE = 'cancelled';
	protected string $title = 'Cancelled';
	protected int $priority = 80;
	
	protected static array $flags_map = [
		'cancelled' => true,
		
		'returned' => null,
		'delivered' => null,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => null,
		'dispatch_started' => null,
		'dispatched' => null,
	
	
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function createEvent( Order|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ) : Order_Event
	{
		return $item->createEvent( Order_Event_Cancel::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}