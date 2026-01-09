<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;
use JetApplication\OrderPersonalReceipt_Status;
use JetApplication\OrderPersonalReceipt_Status_Cancel;
use JetApplication\OrderPersonalReceipt_Status_InProgress;

abstract class Core_OrderPersonalReceipt_Status_Pending extends OrderPersonalReceipt_Status {
	
	public const CODE = 'pending';
	protected string $title = 'Awaiting processing';
	protected int $priority = 1;
	protected static bool $is_editable = true;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function createEvent( EShopEntity_Basic|OrderPersonalReceipt $item, EShopEntity_Status $previouse_status ): null|EShopEntity_Event|OrderPersonalReceipt_Event
	{
		return null;
	}
	
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = OrderPersonalReceipt_Status_InProgress::getAsPossibleFutureStatus();
		$res[] = OrderPersonalReceipt_Status_Cancel::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}