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
		
		$statuses[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button(Tr::_('Done - sent'))->setClass(UI_button::CLASS_SUCCESS);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return WarehouseManagement_TransferBetweenWarehouses_Status_Sent::get();
			}
		};
		
		$statuses[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button(Tr::_('Cancel'))->setClass(UI_button::CLASS_DANGER);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return WarehouseManagement_TransferBetweenWarehouses_Status_Cancelled::get();
			}
		};
		
		
		return $statuses;
	}
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_TransferBetweenWarehouses $item,
		EShopEntity_Status|WarehouseManagement_TransferBetweenWarehouses_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_TransferBetweenWarehouses_Event_New() );
	}
	
}