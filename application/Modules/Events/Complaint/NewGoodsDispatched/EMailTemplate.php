<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Events\Complaint\NewGoodsDispatched;



use Jet\Tr;
use JetApplication\Complaint_EMailTemplate;

class EMailTemplate extends Complaint_EMailTemplate
{
	protected function init(): void
	{
		$this->setInternalName(Tr::_('Complaint - new goods dispatched'));
		$this->setInternalNotes('');
		
		$this->initCommonProperties();
	}
}