<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Exports\GoogleSitemap;


use Jet\MVC_Controller_Default;
use JetApplication\Availabilities;
use JetApplication\Pricelists;
use JetApplication\EShops;


class Controller_Main extends MVC_Controller_Default
{
	public function generate_products_Action() : void
	{
		/**
		 * @var Main $moduel
		 */
		$modula = $this->module;
		$modula->generateExports_products(
			EShops::getCurrent(),
			Pricelists::getCurrent(),
			Availabilities::getCurrent()
		);
		
	}
}