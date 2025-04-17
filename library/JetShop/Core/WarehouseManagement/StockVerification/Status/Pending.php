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
use JetApplication\WarehouseManagement_StockVerification_Event_New;
use JetApplication\WarehouseManagement_StockVerification_Status;
use JetApplication\WarehouseManagement_StockVerification_Status_Cancelled;
use JetApplication\WarehouseManagement_StockVerification_Status_Done;

abstract class Core_WarehouseManagement_StockVerification_Status_Pending extends WarehouseManagement_StockVerification_Status
{
	
	public const CODE = 'pending';
	
	protected bool $cancel_allowed = false;
	
	protected bool $editable = true;
	protected bool $pending = true;
	
	public function __construct()
	{
		$this->title = Tr::_('Pending', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 10;
	}
	
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
				return UI::button(Tr::_('Done'))->setClass(UI_button::CLASS_SUCCESS);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return WarehouseManagement_StockVerification_Status_Done::get();
			}
		};
		
		$statuses[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			public function getButton(): UI_button
			{
				return UI::button(Tr::_('Cancel'))->setClass(UI_button::CLASS_DANGER);
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return WarehouseManagement_StockVerification_Status_Cancelled::get();
			}
		};
		
		
		return $statuses;
	}
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_StockVerification $item,
		EShopEntity_Status|WarehouseManagement_StockVerification_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_StockVerification_Event_New() );
	}
	
}