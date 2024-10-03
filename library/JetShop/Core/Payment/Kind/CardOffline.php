<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Payment_Kind;

abstract class Core_Payment_Kind_CardOffline extends Payment_Kind {
	public const CODE = 'card';
	
	public function __construct()
	{
		$this->setTitle( Tr::_('Card - offline', dictionary: Tr::COMMON_DICTIONARY) );
		$this->setTitleInvoice( Tr::_('Card', dictionary: Tr::COMMON_DICTIONARY) );
		$this->setModuleIsRequired( true );
		
		
	}
	
}