<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\MoneyRefund_Status;

abstract class Core_MoneyRefund_Status_InProcessing extends MoneyRefund_Status {
	
	public const CODE = 'in_processing';
	
	public function __construct()
	{
		$this->title = Tr::_('In Processing', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 20;
	}
	
}