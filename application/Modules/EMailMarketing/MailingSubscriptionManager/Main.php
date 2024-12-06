<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\EMailMarketing\MailingSubscriptionManager;


use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\EMailMarketing_Subscribe_Manager;
use JetApplication\EShop;

class Main extends EMailMarketing_Subscribe_Manager
{
	
	public function showStatus( EShop $eshop, string $email ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($eshop, $email) {
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('eshop', $eshop);
				$view->setVar('email', $email);
				
				return $view->render('status');
			}
		);
	}

}