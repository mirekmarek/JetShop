<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event;
use JetApplication\MoneyRefund_Event_NewRequest;
use JetApplication\MoneyRefund_Status;
use JetApplication\MoneyRefund_Status_Cancelled;
use JetApplication\MoneyRefund_Status_InProcessing;

abstract class Core_MoneyRefund_Status_New extends MoneyRefund_Status {
	
	public const CODE = 'new';
	protected string $title = 'New';
	protected int $priority = 10;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function createEvent( EShopEntity_Basic|MoneyRefund $item, EShopEntity_Status $previouse_status ): ?MoneyRefund_Event
	{
		return $item->createEvent( MoneyRefund_Event_NewRequest::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		
		$res[] = MoneyRefund_Status_InProcessing::getAsPossibleFutureStatus();
		$res[] = MoneyRefund_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
}