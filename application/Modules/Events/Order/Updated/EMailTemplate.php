<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\Order\Updated;

use Jet\Tr;
use JetApplication\Order_EMailTemplate;

class EMailTemplate extends Order_EMailTemplate {
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Order - changed'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}