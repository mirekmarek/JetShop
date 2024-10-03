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
use JetApplication\Shops_Shop;

class Main extends EMailMarketing_Subscribe_Manager
{
	
	public function showStatus( Shops_Shop $shop, string $email ) : string
	{
		return Tr::setCurrentDictionaryTemporary(
			dictionary: $this->module_manifest->getName(),
			action: function() use ($shop, $email) {
				
				$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
				$view->setVar('shop', $shop);
				$view->setVar('email', $email);
				
				return $view->render('status');
			}
		);
	}

}