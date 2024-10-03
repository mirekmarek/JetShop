<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status_New extends ReturnOfGoods_Status {
	
	public const CODE = 'new';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => false,
		'being_processed' => false,
		
		'rejected' => false,
		
		'accepted' => false,
		
		'money_refund' => false,
	];

	
	public function __construct()
	{
		$this->title = Tr::_('New', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 20;
	}
	
}