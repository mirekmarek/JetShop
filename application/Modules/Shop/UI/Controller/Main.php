<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\UI;

use Jet\MVC_Controller_Default;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	public function breadcrumbNavigation_Action(): void
	{
		$this->output( 'breadcrumb_navigation');
	}

}