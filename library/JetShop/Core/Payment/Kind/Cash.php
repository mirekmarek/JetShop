<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Payment_Kind;

abstract class Core_Payment_Kind_Cash extends Payment_Kind {
	public const CODE = 'cash';
	
	public function __construct()
	{
		$this->setTitle( 'Cash' );
	}
}