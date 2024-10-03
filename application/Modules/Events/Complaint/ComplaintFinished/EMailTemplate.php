<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\Complaint\ComplaintFinished;

use Jet\Tr;
use JetApplication\Complaint_EMailTemplate;

class EMailTemplate extends Complaint_EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Complaint - confirmation'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}