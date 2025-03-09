<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Order_Status;

abstract class Core_Order_Status_WaitingForGoodsToBeStocked extends Order_Status {
	
	public const CODE = 'waiting_for_goods_to_be_stocked';
	
	protected static array $flags_map = [
		'cancelled' => false,
		'dispatched' => false,
		'dispatch_started' => false,
		'delivered' => false,
		'returned' => false,
		'all_items_available' => false,
		'ready_for_dispatch' => false,
		
		'paid' => true,
		
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Waiting for goods to be stocked', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 20;
	}
	
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-warning';
	}
	
}