<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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