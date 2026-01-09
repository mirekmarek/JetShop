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
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\Supplier_GoodsOrder_Event_NewOrder;
use JetApplication\Supplier_GoodsOrder_Status;
use JetApplication\Supplier_GoodsOrder_Status_Cancelled;
use JetApplication\Supplier_GoodsOrder_Status_SentToSupplier;

abstract class Core_Supplier_GoodsOrder_Status_Pending extends Supplier_GoodsOrder_Status
{
	
	public const CODE = 'pending';
	protected string $title = 'Pending';
	protected int $priority = 10;
	protected bool $order_can_be_updated = true;
	protected bool $send_allowed = true;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$statuses = [];
		
		$statuses[] = Supplier_GoodsOrder_Status_SentToSupplier::getAsPossibleFutureStatus();
		$statuses[] = Supplier_GoodsOrder_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $statuses;
	}
	
	public function createEvent( EShopEntity_Basic|Supplier_GoodsOrder $item, EShopEntity_Status|Supplier_GoodsOrder_Status $previouse_status ): ?EShopEntity_Event
	{
		return $item->createEvent( new Supplier_GoodsOrder_Event_NewOrder() );
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}