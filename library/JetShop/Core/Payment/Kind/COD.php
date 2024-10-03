<?php
namespace JetShop;

use Jet\Tr;
use JetApplication\Payment_Kind;

abstract class Core_Payment_Kind_COD extends Payment_Kind {
	public const CODE = 'COD';
	
	public function __construct()
	{
		$this->setTitle( Tr::_('COD', dictionary: Tr::COMMON_DICTIONARY) );
		$this->setIsCOD( true );
	}
}