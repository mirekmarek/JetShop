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
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\EShopEntity_VirtualStatus;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Event_Cancelled;
use JetApplication\ReturnOfGoods_Status;
use JetApplication\ReturnOfGoods_Status_Cancelled;

abstract class Core_ReturnOfGoods_Status_Cancelled extends ReturnOfGoods_Status {
	
	public const CODE = 'cancelled';
	protected string $title = 'Cancelled';
	protected int $priority = 60;
	
	protected static array $flags_map = [
		'cancelled' => true,
		
		'completed' => null,
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	
	public function createEvent( ReturnOfGoods|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?ReturnOfGoods_Event
	{
		return $item->createEvent( ReturnOfGoods_Event_Cancelled::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Cancel') )->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ReturnOfGoods_Status_Cancelled::get();
			}
			
			public function noteForCustomerEnabled() : bool
			{
				return true;
			}
			
			public function doNotSendNotificationsSwitchEnabled() : bool
			{
				return true;
			}
		};
	}
	
}