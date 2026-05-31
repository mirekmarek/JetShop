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
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_VirtualStatus;

abstract class Core_OrderPersonalReceipt_VirtualStatus extends EShopEntity_VirtualStatus {

	protected static string $base_status_class = OrderPersonalReceipt_VirtualStatus::class;
	
	abstract public static function handle(
		EShopEntity_HasStatus_Interface|OrderPersonalReceipt $item,
		bool $handle_event=true,
		array $params=[],
		?Closure $event_setup=null
	) : void;
	
}