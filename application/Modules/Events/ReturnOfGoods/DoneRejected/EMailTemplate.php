<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
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