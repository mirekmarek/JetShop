<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Event_Rejected;
use JetApplication\Complaint_Status;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;

abstract class Core_Complaint_Status_Rejected extends Complaint_Status {
	
	public const CODE = 'rejected';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => true,
		
		'being_processed' => null,
		'clarification_required' => null,
		
		'accepted' => false,
		'money_refund' => false,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => false,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Rejected', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 50;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
	public function createEvent( Complaint|EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?Complaint_Event
	{
		return $item->createEvent( Complaint_Event_Rejected::new() );
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}