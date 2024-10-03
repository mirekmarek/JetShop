<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Order_Status;

abstract class Core_Order_Status_ReadyForDispatch extends Order_Status {
	
	public const CODE = 'ready_for_dispatch';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'dispatch_started' => false,
		'delivered' => false,
		'returned' => false,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => true,
		

	];
	
	public function __construct()
	{
		$this->title = Tr::_('Ready for dispatch', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 30;
	}
	
}