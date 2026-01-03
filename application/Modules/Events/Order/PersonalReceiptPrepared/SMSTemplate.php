<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\PersonalReceiptPrepared;


use Jet\Tr;
use JetApplication\Order_SMSTemplate;

class SMSTemplate extends Order_SMSTemplate {
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Order - personal receipt prepared'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
	
}