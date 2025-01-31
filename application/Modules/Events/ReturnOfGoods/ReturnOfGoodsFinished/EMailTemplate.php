<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
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