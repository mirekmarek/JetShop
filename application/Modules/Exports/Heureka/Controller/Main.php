<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Exports\Heureka;

use Jet\MVC_Controller_Default;
use JetApplication\Availabilities;
use JetApplication\Pricelists;
use JetApplication\Shops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	public function generate_products_Action() : void
	{
		/**
		 * @var Main $moduel
		 */
		$modula = $this->module;
		$modula->generateExports_products(
			Shops::getCurrent(),
			Pricelists::getCurrent(),
			Availabilities::getCurrent()
		);
		
	}
}