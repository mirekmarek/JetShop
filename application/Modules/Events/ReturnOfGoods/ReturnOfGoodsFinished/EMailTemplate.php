<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Events\ReturnOfGoods\ReturnOfGoodsFinished;

use JetApplication\ReturnOfGoods_EMailTemplate;

class EMAilTemplate extends ReturnOfGoods_EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName('Return of goods - confirmation');
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}