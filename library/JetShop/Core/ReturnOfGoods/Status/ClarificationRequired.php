<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Event_ClarificationRequired;
use JetApplication\ReturnOfGoods_Status;
use JetApplication\ReturnOfGoods_Status_BeingProcessed;
use JetApplication\ReturnOfGoods_Status_Cancelled;

abstract class Core_ReturnOfGoods_Status_ClarificationRequired extends ReturnOfGoods_Status {
	
	public const CODE = 'clarification_required';
	protected string $title = 'Clarification required';
	protected int $priority = 40;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => true,
		'being_processed' => true,
		
		'rejected' => false,
		
		'accepted' => false,
		
		'money_refund' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
	public function createEvent( ReturnOfGoods|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?ReturnOfGoods_Event
	{
		return $item->createEvent( ReturnOfGoods_Event_ClarificationRequired::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = ReturnOfGoods_Status_BeingProcessed::getAsPossibleFutureStatus();
		$res[] = ReturnOfGoods_Status_Cancelled::getAsPossibleFutureStatus();
		$res[] = ReturnOfGoods_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
}