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
use JetApplication\MoneyRefund_Event_ProcessingStarted;
use JetApplication\MoneyRefund_Status;
use JetApplication\MoneyRefund_Status_Cancelled;
use JetApplication\MoneyRefund_Status_Done;
use JetApplication\MoneyRefund_VirtualStatus_Rollback;

abstract class Core_MoneyRefund_Status_InProcessing extends MoneyRefund_Status {
	
	public const CODE = 'in_processing';
	protected string $title = 'In Processing';
	protected int $priority = 20;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function createEvent( EShopEntity_Basic|MoneyRefund $item, EShopEntity_Status $previouse_status ): ?MoneyRefund_Event
	{
		return $item->createEvent( MoneyRefund_Event_ProcessingStarted::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{

		$res[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Done') )
					->setClass( UI_button::CLASS_SUCCESS );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return MoneyRefund_Status_Done::get();
			}
		};
		
		$res[] = new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Cancel') )
					->setClass( UI_button::CLASS_DANGER );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return MoneyRefund_Status_Cancelled::get();
			}
		};
		
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