<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Events\Complaint\Updated;


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