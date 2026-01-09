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
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;
use JetApplication\OrderPersonalReceipt_Event_PreparationStarted;
use JetApplication\OrderPersonalReceipt_Status;
use JetApplication\OrderPersonalReceipt_Status_Cancel;
use JetApplication\OrderPersonalReceipt_Status_InProgress;
use JetApplication\OrderPersonalReceipt_Status_Prepared;

abstract class Core_OrderPersonalReceipt_Status_InProgress extends OrderPersonalReceipt_Status {
	
	public const CODE = 'in_progress';
	protected string $title = 'In progress';
	protected int $priority = 2;
	protected static bool $is_rollback_possible = true;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function createEvent( EShopEntity_Basic|OrderPersonalReceipt $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderPersonalReceipt_Event
	{
		return OrderPersonalReceipt_Event_PreparationStarted::new();
	}
	
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = OrderPersonalReceipt_Status_Prepared::getAsPossibleFutureStatus();
		$res[] = OrderPersonalReceipt_Status_Cancel::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('In progress') )->setClass( UI_button::CLASS_INFO );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return OrderPersonalReceipt_Status_InProgress::get();
			}
		};
	}
	
}