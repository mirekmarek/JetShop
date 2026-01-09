<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Order_Status;
use JetApplication\Order_Status_Cancelled;

abstract class Core_Order_Status_WaitingForGoodsToBeStocked extends Order_Status {
	
	public const CODE = 'waiting_for_goods_to_be_stocked';
	protected string $title = 'Waiting for goods to be stocked';
	protected int $priority = 20;
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'dispatch_started' => false,
		'delivered' => false,
		'returned' => false,
		'all_items_available' => false,
		'ready_for_dispatch' => false,
		
		'paid' => true,
		
	];
	
	public function __construct()
	{
	}
	
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
	public function createEvent( EShopEntity_Basic|Order $item, EShopEntity_Status $previouse_status ): ?Order_Event
	{
		return null;
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