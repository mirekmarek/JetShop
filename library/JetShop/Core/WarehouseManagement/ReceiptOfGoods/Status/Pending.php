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
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Event_New;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Status;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Status_Cancelled;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Status_Done;

abstract class Core_WarehouseManagement_ReceiptOfGoods_Status_Pending extends WarehouseManagement_ReceiptOfGoods_Status
{
	
	public const CODE = 'pending';
	protected string $title = 'Pending';
	protected int $priority = 10;
	
	protected bool $cancel_allowed = false;
	
	protected bool $editable = true;
	protected bool $pending = true;

	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$statuses = [];
		
		$statuses[] = WarehouseManagement_ReceiptOfGoods_Status_Done::getAsPossibleFutureStatus();
		$statuses[] = WarehouseManagement_ReceiptOfGoods_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $statuses;
	}
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_ReceiptOfGoods $item,
		EShopEntity_Status|WarehouseManagement_ReceiptOfGoods_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_ReceiptOfGoods_Event_New() );
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}