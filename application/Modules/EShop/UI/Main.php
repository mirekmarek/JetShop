<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\EShop\UI;

use Jet\Application_Module;
use JetApplication\EShop_Managers_UI;
use JetApplication\EShop_ModuleUsingTemplate_Interface;
use JetApplication\EShop_ModuleUsingTemplate_Trait;

/**
 *
 */
class Main extends Application_Module implements EShop_Managers_UI, EShop_ModuleUsingTemplate_Interface
{
	use EShop_ModuleUsingTemplate_Trait;
	
	public function renderBreadcrumbNavigation(): string
	{
		return $this->getView()->render( 'breadcrumb-navigation' );
	}

}