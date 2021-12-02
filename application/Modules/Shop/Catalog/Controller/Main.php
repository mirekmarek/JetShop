<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\Catalog;

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

	use Controller_Main_Category;
	use Controller_Main_Product;

	protected ?MVC_Controller_Router $router = null;


	public function getControllerRouter() : MVC_Controller_Router
	{

		if( !$this->router ) {
			$this->router = new MVC_Controller_Router( $this );

			//$this->router->setDefaultAction('homepage');

			$main_router = MVC::getRouter();
			$path = $main_router->getUrlPath();

			if($path) {
				$path = explode('/', $path);
				$path_base= explode('-', $path[0]);

				if(count($path_base)>1) {

					$i = count($path_base);

					$object_type = $path_base[$i-2];
					$object_id = (int)$path_base[$i-1];


					if($object_id>0) {

						if($object_type=='c') {
							$this->getControllerRouter_category( $object_id, $path );
						}

						if($object_type=='p') {
							$this->getControllerRouter_product( $object_id, $path );
						}
					}
				}


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
