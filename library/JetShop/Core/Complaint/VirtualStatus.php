<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_VirtualStatus;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_VirtualStatus;

abstract class Core_Complaint_VirtualStatus extends EShopEntity_VirtualStatus {
	
	protected static string $base_status_class = Complaint_VirtualStatus::class;
	
	protected static array $flags_map = [
		'completed' => null,
		'cancelled' => null,
		
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
		'sent_for_repair' => null,
		'repaired' => null,
		'send_new_products' => null,
	];
	
	protected static ?array $list = null;
	
	abstract public function createEvent( EShopEntity_Basic|Complaint $item, EShopEntity_Status $previouse_status ): ?Complaint_Event;
	
}