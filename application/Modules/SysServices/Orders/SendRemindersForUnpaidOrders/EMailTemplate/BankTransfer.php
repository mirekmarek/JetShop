<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Orders\SendRemindersForUnpaidOrders;

use Jet\Tr;
use JetApplication\Order_EMailTemplate;

class EMailTemplate_BankTransfer extends Order_EMailTemplate {
	
	protected function init(): void
	{
		$this->setInternalName( Tr::_('Unpaid order reminder - bank transfer') );
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}