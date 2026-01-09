<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use Jet\UI;
use Jet\UI_button;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Event_Cancelled;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Status;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Status_Cancelled;

abstract class Core_WarehouseManagement_ReceiptOfGoods_Status_Cancelled extends WarehouseManagement_ReceiptOfGoods_Status
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
		EShopEntity_Basic|WarehouseManagement_ReceiptOfGoods $item,
		EShopEntity_Status|WarehouseManagement_ReceiptOfGoods_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_ReceiptOfGoods_Event_Cancelled() );
	}
	
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button(Tr::_('Cancel'))->setClass(UI_button::CLASS_DANGER);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return WarehouseManagement_ReceiptOfGoods_Status_Cancelled::get();
			}
		};
	}
	
}