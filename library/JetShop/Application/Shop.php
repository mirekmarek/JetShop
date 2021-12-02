<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Exception;
use Jet\Logger;

use Jet\MVC_Router;

use Jet\Auth;
use Jet\SysConf_Jet_ErrorPages;
use Jet\SysConf_Jet_Form;
use Jet\SysConf_Jet_UI;

/**
 *
 */
class Application_Shop
{

	/**
	 * @param MVC_Router $router
	 */
	public static function init( MVC_Router $router ) : void
	{
		Application::initErrorPages( $router );
		Logger::setLogger( new Logger_Shop() );
		Auth::setController( new Customer_AuthController() );

		if(!Shops::determineByBase( $router->getBase()->getId(), $router->getLocale() )) {
			throw new Exception('Unknown shop');
		}

		SysConf_Jet_UI::setViewsDir( $router->getBase()->getViewsPath() . 'ui/' );
		SysConf_Jet_Form::setDefaultViewsDir( $router->getBase()->getViewsPath() . 'form/' );
		SysConf_Jet_ErrorPages::setErrorPagesDir( $router->getBase()->getPagesDataPath( $router->getLocale() ) );

	}

}