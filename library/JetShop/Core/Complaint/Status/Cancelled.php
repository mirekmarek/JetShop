<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_Cancelled;
use JetApplication\Complaint_Status;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;

abstract class Core_Complaint_Status_Cancelled extends Complaint_Status {
	
	public const CODE = 'cancelled';
	protected string $title = 'Cancelled';
	protected int $priority = 60;
	
	protected static array $flags_map = [
		'cancelled' => true,
		
		'completed' => null,
		'rejected' => null,
		
		'being_processed' => null,
		'clarification_required' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
		'sent_for_repair' => null,
		'repaired' => null,
		'send_new_products' => null,
	];

	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_Cancelled::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}