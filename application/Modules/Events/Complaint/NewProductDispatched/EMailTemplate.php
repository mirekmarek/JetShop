<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\Complaint\NewProductDispatched;

use JetApplication\Complaint_EMailTemplate;
use Jet\Tr;

class EMailTemplate extends Complaint_EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Complaint - accepted - new product dispatched'));
		$this->initCommonProperties();
	}
}