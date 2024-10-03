<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Complaint_Status;

abstract class Core_Complaint_Status_BeingProcessed extends Complaint_Status {
	
	public const CODE = 'being_processed';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'rejected' => false,
		
		'clarification_required' => false,
		'being_processed' => true,
		
		'accepted' => false,
		
		'money_refund' => false,
		'sent_for_repair' => false,
		'repaired' => false,
		'send_new_products' => false,
		
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Being processed', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 30;
	}
	
}