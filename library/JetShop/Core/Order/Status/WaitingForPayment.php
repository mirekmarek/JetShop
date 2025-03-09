<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Order_Status;

abstract class Core_Order_Status_WaitingForPayment extends Order_Status {
	
	public const CODE = 'waiting_for_payment';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'dispatch_started' => false,
		'delivered' => false,
		'returned' => false,
		'ready_for_dispatch' => false,
		
		'payment_required' => true,
		'paid' => false,
		
		'all_items_available' => null,
		
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Waiting for payment', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 10;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return '';
	}
	
	public function getShowAdminCSSStyle() : string
	{
		return 'background-color: #ffaaaaaa;color: #111111;font-weight: bolder;';
	}
	
}