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
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_StockVerification_Event_Done;
use JetApplication\WarehouseManagement_StockVerification_Status;
use JetApplication\WarehouseManagement_StockVerification_Status_Done;

abstract class Core_WarehouseManagement_StockVerification_Status_Done extends WarehouseManagement_StockVerification_Status
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
		EShopEntity_Basic|WarehouseManagement_StockVerification $item,
		EShopEntity_Status|WarehouseManagement_StockVerification_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_StockVerification_Event_Done() );
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button(Tr::_('Done'))->setClass(UI_button::CLASS_SUCCESS);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return WarehouseManagement_StockVerification_Status_Done::get();
			}
		};
	}
	
}