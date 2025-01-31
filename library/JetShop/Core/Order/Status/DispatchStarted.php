<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Order_Status;

abstract class Core_Order_Status_DispatchStarted extends Order_Status {
	
	public const CODE = 'dispatch_started';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'delivered' => false,
		'returned' => false,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => true,
		'dispatch_started' => true,
	
	
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Dispatch started', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 40;
	}
	
}