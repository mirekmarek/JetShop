<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Payment_Kind;

abstract class Core_Payment_Kind_BankTransfer extends Payment_Kind {
	public const CODE = 'bank_transfer';
	
	public function __construct()
	{
		$this->setTitle( Tr::_('Bank transfer', dictionary: Tr::COMMON_DICTIONARY) );
		$this->setModuleIsRequired( true );
	}
	
}