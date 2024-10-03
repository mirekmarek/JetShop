<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Complaint_DispatchStatus;

abstract class Core_Complaint_DispatchStatus_Returned extends Complaint_DispatchStatus {
	
	public const CODE = 'delivered';
	
	protected static array $flags_map = [
		'ready_for_dispatch' => true,
		'dispatch_started' => true,
		
		'dispatched' => true,
		'delivered' => false,
		'returned' => true,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Returned', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 50;
	}
	
}