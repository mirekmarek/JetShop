<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\Order\Cancel;


use Jet\Tr;
use JetApplication\Order_EMailTemplate;

class EMailTemplate extends Order_EMailTemplate {
	
	public function init(): void
	{
		
		$this->setInternalName( Tr::_( 'Order - canceled' ) );
		$this->setInternalNotes( '' );
		
		$this->initCommonProperties();
	}
	
}