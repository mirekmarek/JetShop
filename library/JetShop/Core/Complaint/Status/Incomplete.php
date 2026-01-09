<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_Status_AcceptedMoneyRefunded;
use JetApplication\Complaint_Status_Advice;
use JetApplication\Complaint_Status_Cancelled;
use JetApplication\Complaint_Status_GoodsReceived;
use JetApplication\Complaint_Status_PickupOrdered;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;

abstract class Core_Complaint_Status_Incomplete extends Complaint_Status {
	
	public const CODE = 'incomplete';
	protected string $title = 'Incomplete';
	protected int $priority = 10;
	
	protected static array $flags_map = [
		'completed' => false,
		'cancelled' => false,
		
		'clarification_required' => null,
		'being_processed' => null,
		'rejected' => null,
		'accepted' => null,
		'money_refund' => null,
		'sent_for_repair' => null,
		'repaired' => null,
		'send_new_products' => null,
		
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return null;
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = Complaint_Status_PickupOrdered::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_GoodsReceived::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_AcceptedMoneyRefunded::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_Advice::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_Cancelled::getAsPossibleFutureStatus();
		
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
	
}