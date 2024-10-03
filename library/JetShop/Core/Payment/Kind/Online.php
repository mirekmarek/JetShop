<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Payment_Kind;
use JetApplication\Payment_Kind_BankTransfer;

abstract class Core_Payment_Kind_Online extends Payment_Kind {
	public const CODE = 'online_payment';
	
	public function __construct()
	{
		$this->setTitle( Tr::_('General online bank transfer', dictionary: Tr::COMMON_DICTIONARY) );
		$this->setModuleIsRequired( true );
		$this->setIsOnlinePayment( true );
		$this->setTitleInvoice( Tr::_('Bank transfer', dictionary: Tr::COMMON_DICTIONARY) );
		
		$this->setAllowedForInvoices( false );
		$this->setAlternativeKindForInvoices( new Payment_Kind_BankTransfer() );
	}
	
}