<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Status;

abstract class Core_Order_Status extends EShopEntity_Status {

	protected static string $base_status_class = Order_Status::class;
	
	protected static array $flags_map = [
		'cancelled' => null,
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		'ready_for_dispatch' => null,
		'dispatch_started' => null,
		'dispatched' => null,
		'delivered' => null,
		'returned' => null,
	];
	
	protected static ?array $list = null;
	
	public function createEvent( EShopEntity_Basic|Order $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|Order_Event
	{
		return null;
	}
	
}