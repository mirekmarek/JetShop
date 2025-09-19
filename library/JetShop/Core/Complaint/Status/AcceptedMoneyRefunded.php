<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_AcceptedMoneyRefund;
use JetApplication\Complaint_Status;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;

abstract class Core_Complaint_Status_AcceptedMoneyRefunded extends Complaint_Status {
	
	public const CODE = 'accepted_money_refunded';
	protected string$title = 'Accepted - Money Refunded';
	protected int $priority = 70;
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => false,
		
		'clarification_required' => null,
		'being_processed' => null,
		
		'accepted' => true,
		
		'money_refund' => true,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => false,
	];
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_AcceptedMoneyRefund::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}