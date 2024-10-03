<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Complaint_DispatchStatus;

abstract class Core_Complaint_DispatchStatus_NotReadyForDispatch extends Complaint_DispatchStatus {
	
	public const CODE = 'not_ready_for_dispatch';
	
	protected static array $flags_map = [
		'ready_for_dispatch' => false,
		'dispatch_started' => false,
		
		'dispatched' => false,
		'delivered' => false,
		'returned' => false,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Not ready for dispatch', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 10;
	}
	
}