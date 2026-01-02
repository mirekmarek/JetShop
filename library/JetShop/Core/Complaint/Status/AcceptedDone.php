<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_Accepted;
use JetApplication\Complaint_Status;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;

abstract class Core_Complaint_Status_AcceptedDone extends Complaint_Status {
	
	public const CODE = 'accepted_done';
	protected string $title = 'Accepted - Done';
	protected int $priority = 110;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => false,
		
		'clarification_required' => null,
		'being_processed' => null,
		
		'accepted' => true,
		
		'money_refund' => null,
		'sent_for_repair' => null,
		'repaired' => null,
		'send_new_products' => null,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_Accepted::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		$res = [];
		
		
		return $res;
	}
	
}