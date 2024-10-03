<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status_Cancelled extends ReturnOfGoods_Status {
	
	public const CODE = 'cancelled';
	
	protected static array $flags_map = [
		'cancelled' => true,
		
		'completed' => null,
		'clarification_required' => null,
		'being_processed' => null,
		
		'rejected' => null,
		
		'accepted' => null,
		
		'money_refund' => null,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Cancelled', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 60;
	}
	
}