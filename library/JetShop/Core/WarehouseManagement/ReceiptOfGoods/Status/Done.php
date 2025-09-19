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
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Event_Done;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Status;

abstract class Core_WarehouseManagement_ReceiptOfGoods_Status_Done extends WarehouseManagement_ReceiptOfGoods_Status
{
	
	public const CODE = 'done';
	protected string $title = 'Done';
	protected int $priority = 50;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_ReceiptOfGoods $item,
		EShopEntity_Status|WarehouseManagement_ReceiptOfGoods_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_ReceiptOfGoods_Event_Done() );
	}
	
}