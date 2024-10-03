<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status_ClarificationRequired extends ReturnOfGoods_Status {
	
	public const CODE = 'clarification_required';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => true,
		'being_processed' => true,
		
		'rejected' => false,
		
		'accepted' => false,
		
		'money_refund' => false,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Clarification required', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 40;
	}
	
}