<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\ReturnOfGoods\DoneAccepted;

use JetApplication\ReturnOfGoods_EMailTemplate;

class EMailTemplate extends ReturnOfGoods_EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName('Return of goods - done - accepted');
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}