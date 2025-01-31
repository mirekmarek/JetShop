<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Events\ReturnOfGoods\Updated;


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