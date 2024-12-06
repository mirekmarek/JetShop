<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\Complaint\Updated;

use Jet\Tr;
use JetApplication\Complaint_EMailTemplate;

class EMailTemplate extends Complaint_EMailTemplate {
	
	public function init() : void
	{
		$this->setInternalName(Tr::_('Complaint - changed'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}