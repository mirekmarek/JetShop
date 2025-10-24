<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\MailingSubscriptionManager;



use Jet\Factory_MVC;
use Jet\Tr;
use JetApplication\Application_Service_General_EMailMarketingSubscribeManager;
use JetApplication\EShop;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;

class Main extends Application_Service_General_EMailMarketingSubscribeManager implements EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
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