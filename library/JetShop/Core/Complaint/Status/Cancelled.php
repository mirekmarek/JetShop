<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Complaint_Status;

abstract class Core_Complaint_Status_Cancelled extends Complaint_Status {
	
	public const CODE = 'cancelled';
	
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
	
	public function __construct()
	{
		$this->title = Tr::_('Cancelled', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 60;
	}
	
}