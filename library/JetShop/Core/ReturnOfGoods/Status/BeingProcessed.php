<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\ReturnOfGoods_Status;

abstract class Core_ReturnOfGoods_Status_BeingProcessed extends ReturnOfGoods_Status {
	
	public const CODE = 'being_processed';
	
	protected static array $flags_map = [
		'cancelled' => false,
		
		'completed' => true,
		'clarification_required' => null,
		'being_processed' => true,
		
		'rejected' => false,
		
		'accepted' => false,
		
		'money_refund' => false,
	];
	
	public function __construct()
	{
		$this->title = Tr::_('Being processed', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 30;
	}
	
}