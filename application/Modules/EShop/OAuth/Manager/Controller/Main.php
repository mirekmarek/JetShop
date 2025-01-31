<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\EShop\OAuth\Manager;


use Jet\MVC;
use Jet\MVC_Controller_Default;
use JetApplication\EShop_OAuth_BackendModule;


class Controller_Main extends MVC_Controller_Default
{
	protected EShop_OAuth_BackendModule $current_module;
	
	public function resolve(): bool|string
	{
		/**
		 * @var Main $main
		 */
		$core = $this->module;
		$modules = $core->getOAuthModules();
		
		$main_router = MVC::getRouter();
		$service_id = $main_router->getUrlPath();
		
		
		if(!$service_id || !isset($modules[$service_id])) {
			$main_router->setIs404();
			return false;
		}
		
		$main_router->setUsedUrlPath($service_id);
		
		$this->current_module = $modules[$service_id];
		
		return true;
	}
	
	public function default_Action() : void
	{
		/**
		 * @var Main $main
		 */
		$core = $this->module;
		
		$core->handle( $this->current_module );
	}
}