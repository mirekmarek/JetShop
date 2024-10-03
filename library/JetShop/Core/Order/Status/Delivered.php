<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Order_Status;

abstract class Core_Order_Status_Delivered extends Order_Status {
	
	public const CODE = 'delivered';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'returned' => false,
		
		'delivered' => true,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => null,
		'dispatch_started' => null,
		'dispatched' => null,
	
	
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Delivered', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 60;
	}
	
}