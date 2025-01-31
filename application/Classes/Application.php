<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\Application as Jet_Application;

use Jet\MVC_Router;

use Jet\SysConf_Jet_ErrorPages;

/**
 *
 */
class Application extends Jet_Application
{

	/**
	 * @param MVC_Router $router
	 */
	public static function initErrorPages( MVC_Router $router ) : void
	{
		SysConf_Jet_ErrorPages::setErrorPagesDir( $router->getBase()->getPagesDataPath( $router->getLocale() ) );
	}

}