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
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Event_New;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status_Cancelled;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status_Sent;

abstract class Core_WarehouseManagement_TransferBetweenWarehouses_Status_Pending extends WarehouseManagement_TransferBetweenWarehouses_Status
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
		
		$statuses[] = WarehouseManagement_TransferBetweenWarehouses_Status_Sent::getAsPossibleFutureStatus();
		$statuses[] = WarehouseManagement_TransferBetweenWarehouses_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $statuses;
	}
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_TransferBetweenWarehouses $item,
		EShopEntity_Status|WarehouseManagement_TransferBetweenWarehouses_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_TransferBetweenWarehouses_Event_New() );
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}