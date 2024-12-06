<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\ReturnOfGoods\Updated;

use Jet\Tr;
use JetApplication\ReturnOfGoods_EMailTemplate;

class EMailTemplate extends ReturnOfGoods_EMailTemplate {
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Return of goods - changed'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}