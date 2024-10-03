<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\UI;

use Jet\Application_Module;
use JetApplication\Shop_Managers_UI;
use JetApplication\Shop_ModuleUsingTemplate_Interface;
use JetApplication\Shop_ModuleUsingTemplate_Trait;

/**
 *
 */
class Main extends Application_Module implements Shop_Managers_UI, Shop_ModuleUsingTemplate_Interface
{
	use Shop_ModuleUsingTemplate_Trait;
	
	public function renderBreadcrumbNavigation(): string
	{
		return $this->getView()->render( 'breadcrumb-navigation' );
	}

}