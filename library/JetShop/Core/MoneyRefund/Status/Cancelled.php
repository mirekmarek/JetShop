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
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event;
use JetApplication\MoneyRefund_Event_Cancelled;
use JetApplication\MoneyRefund_Status;
use JetApplication\MoneyRefund_VirtualStatus_Rollback;

abstract class Core_MoneyRefund_Status_Cancelled extends MoneyRefund_Status
{
	
	public const CODE = 'cancelled';
	
	public function __construct()
	{
		$this->title = Tr::_('Cancelled', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 60;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function createEvent( EShopEntity_Basic|MoneyRefund $item, EShopEntity_Status $previouse_status ): ?MoneyRefund_Event
	{
		return $item->createEvent( MoneyRefund_Event_Cancelled::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Rollback') )->setClass( UI_button::CLASS_LIGHT );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return MoneyRefund_VirtualStatus_Rollback::get();
			}
		};
		
		return $res;
	}
	
}