<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Payment_Kind;

abstract class Core_Payment_Kind_Cash extends Payment_Kind {
	public const CODE = 'cash';
	
	public function __construct()
	{
		$this->setTitle( Tr::_('Cash', dictionary: Tr::COMMON_DICTIONARY) );
	}
}