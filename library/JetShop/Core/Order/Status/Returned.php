<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Order_Status;

abstract class Core_Order_Status_Returned extends Order_Status {
	
	public const CODE = 'returned';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'returned' => true,
		
		'delivered' => null,
		
		'payment_required' => null,
		'paid' => null,
		'all_items_available' => null,
		
		'ready_for_dispatch' => null,
		'dispatch_started' => null,
		'dispatched' => null,
	
	
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Returned', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 70;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		return 'background-color: #ffaaaaaa;color: #111111;font-weight: bolder;';
	}
	
}