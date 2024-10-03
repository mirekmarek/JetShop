<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Complaint_Status;

abstract class Core_Complaint_Status_New extends Complaint_Status {
	
	public const CODE = 'new';
	
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
	
	public function __construct()
	{
		$this->title = Tr::_('New', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 20;
	}
	
}