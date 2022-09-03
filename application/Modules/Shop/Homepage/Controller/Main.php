<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\Homepage;

use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router;
use Jet\MVC_Layout;
use Jet\Tr;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	protected ?MVC_Controller_Router $router = null;

	public function getControllerRouter() : MVC_Controller_Router
	{

		if( !$this->router ) {
			$this->router = new MVC_Controller_Router( $this );

			$path = MVC::getRouter()->getUrlPath();

			if(!$path) {
				$this->router->setDefaultAction('homepage');
			}
		}

		return $this->router;
	}

	/**
	 *
	 */
	public function homepage_Action() : void
	{
		Tr::setCurrentDictionary('homepage');

		MVC_Layout::getCurrentLayout()->setScriptName('homepage');

		$this->output('homepage');
	}

}