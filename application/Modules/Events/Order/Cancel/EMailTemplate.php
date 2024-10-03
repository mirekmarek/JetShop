<?php
/**
 *
 * @copyright
 * @license
 * @author
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