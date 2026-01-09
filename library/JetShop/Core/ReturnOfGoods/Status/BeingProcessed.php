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
use JetApplication\ReturnOfGoods_Event_ProcessingStarted;
use JetApplication\ReturnOfGoods_Status;
use JetApplication\ReturnOfGoods_Status_AcceptedMoneyRefunded;
use JetApplication\ReturnOfGoods_Status_BeingProcessed;
use JetApplication\ReturnOfGoods_Status_Cancelled;
use JetApplication\ReturnOfGoods_Status_ClarificationRequired;
use JetApplication\ReturnOfGoods_Status_Rejected;

abstract class Core_ReturnOfGoods_Status_BeingProcessed extends ReturnOfGoods_Status {
	
	public const CODE = 'being_processed';
	protected string $title = 'Being processed';
	protected int $priority = 30;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => null,
		'being_processed' => true,
		
		'rejected' => false,
		
		'accepted' => false,
		
		'money_refund' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = ReturnOfGoods_Status_ClarificationRequired::getAsPossibleFutureStatus();
		$res[] = ReturnOfGoods_Status_AcceptedMoneyRefunded::getAsPossibleFutureStatus();
		$res[] = ReturnOfGoods_Status_Rejected::getAsPossibleFutureStatus();
		$res[] = ReturnOfGoods_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	
	public function createEvent( ReturnOfGoods|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?ReturnOfGoods_Event
	{
		return $item->createEvent( ReturnOfGoods_Event_ProcessingStarted::new() );
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return new class extends EShopEntity_Status_PossibleFutureStatus {
			
			public function getButton(): UI_button
			{
				return UI::button( Tr::_('Start processing') )
					->setClass( UI_button::CLASS_PRIMARY );
			}
			
			public function getStatus(): EShopEntity_Status|EShopEntity_VirtualStatus
			{
				return ReturnOfGoods_Status_BeingProcessed::get();
			}
		};
	}
	
}