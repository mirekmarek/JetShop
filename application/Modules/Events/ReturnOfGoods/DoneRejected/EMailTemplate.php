<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\ReturnOfGoods\DoneRejected;

use JetApplication\ReturnOfGoods_EMailTemplate;

class EMailTemplate extends ReturnOfGoods_EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName('Return of goods - done - rejected');
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}