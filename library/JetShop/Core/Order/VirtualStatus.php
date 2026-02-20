<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Closure;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\Order;
use JetApplication\Order_VirtualStatus;

abstract class Core_Order_VirtualStatus extends EShopEntity_VirtualStatus {
	
	protected static string $base_status_class = Order_VirtualStatus::class;
	
	abstract public static function handle(
		EShopEntity_HasStatus_Interface|Order $item,
		bool $handle_event=true,
		array $params=[],
		?Closure $event_setup=null
	) : void;
	
}