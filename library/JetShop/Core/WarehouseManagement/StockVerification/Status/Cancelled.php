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
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_StockVerification_Event_Cancelled;
use JetApplication\WarehouseManagement_StockVerification_Status;

abstract class Core_WarehouseManagement_StockVerification_Status_Cancelled extends WarehouseManagement_StockVerification_Status
{
	public const CODE = 'cancelled';
	protected string $title = 'Cancelled';
	protected int $priority = 80;
	
	protected bool $cancel_allowed = false;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_StockVerification $item,
		EShopEntity_Status|WarehouseManagement_StockVerification_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_StockVerification_Event_Cancelled() );
	}
	
}