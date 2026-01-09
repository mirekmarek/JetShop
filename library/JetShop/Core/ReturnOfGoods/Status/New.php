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
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\ReturnOfGoods_Event_ReturnOfGoodsFinished;
use JetApplication\ReturnOfGoods_Status;
use JetApplication\ReturnOfGoods_Status_BeingProcessed;
use JetApplication\ReturnOfGoods_Status_Cancelled;

abstract class Core_ReturnOfGoods_Status_New extends ReturnOfGoods_Status {
	
	public const CODE = 'new';
	protected string $title = 'New';
	protected int $priority = 20;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => false,
		'being_processed' => false,
		
		'rejected' => false,
		
		'accepted' => false,
		
		'money_refund' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = ReturnOfGoods_Status_BeingProcessed::getAsPossibleFutureStatus();
		$res[] = ReturnOfGoods_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public function createEvent( ReturnOfGoods|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?ReturnOfGoods_Event
	{
		return $item->createEvent( ReturnOfGoods_Event_ReturnOfGoodsFinished::new() );
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}