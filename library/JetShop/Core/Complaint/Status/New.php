<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_NewComplaintFinished;
use JetApplication\Complaint_Status;
use JetApplication\Complaint_Status_BeingProcessed;
use JetApplication\Complaint_Status_Cancelled;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Status_PossibleFutureStatus;

abstract class Core_Complaint_Status_New extends Complaint_Status {
	
	public const CODE = 'new';
	protected string $title = 'New - awaiting processing';
	protected int $priority = 2;
	
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		
		'clarification_required' => false,
		'being_processed' => false,
		
		'rejected' => false,
		
		'accepted' => false,
		'money_refund' => false,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => false,
		
	];
	
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_NewComplaintFinished::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		$res[] = Complaint_Status_BeingProcessed::getAsPossibleFutureStatus();
		$res[] = Complaint_Status_Cancelled::getAsPossibleFutureStatus();
		
		return $res;
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
}