<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\Exception;
use Jet\Logger;

use Jet\MVC;
use Jet\MVC_Base_Interface;
use Jet\MVC_Router;

use Jet\Auth;
use Jet\SysConf_Jet_ErrorPages;
use Jet\SysConf_Jet_Form;
use Jet\SysConf_Jet_MVC;
use Jet\SysConf_Jet_UI;

/**
 *
 */
class Application_Shop
{
	public static function getBaseId() : string
	{
		return 'shop';
	}
	
	public static function getBase() : MVC_Base_Interface
	{
		return MVC::getBase( static::getBaseId() );
	}

	/**
	 * @param MVC_Router $router
	 */
	public static function init( MVC_Router $router ) : void
	{
		SysConf_Jet_MVC::setUseModulePages( false );
		
		Application::initErrorPages( $router );
		Logger::setLogger( new Logger_Shop() );
		Auth::setController( new Customer_AuthController() );

		if(!Shops::determineByBase( $router->getBase()->getId(), $router->getLocale() )) {
			throw new Exception('Unknown shop');
		}
		
		$shop = Shops::getCurrent();

		if($shop->getUseTemplate()) {
			$template = $shop->getTemplate();
			
			SysConf_Jet_UI::setViewsDir( $template->getUIViewsDir() );
			SysConf_Jet_Form::setDefaultViewsDir( $template->getFormViewsDir() );
			SysConf_Jet_ErrorPages::setErrorPagesDir( $template->getErrorPagesDir() );
			
			$router->getBase()->setLayoutsPath( $template->getLayoutsDir() );
			
		} else {
			SysConf_Jet_UI::setViewsDir( $router->getBase()->getViewsPath() . 'ui/' );
			SysConf_Jet_Form::setDefaultViewsDir( $router->getBase()->getViewsPath() . 'form/' );
			SysConf_Jet_ErrorPages::setErrorPagesDir( $router->getBase()->getPagesDataPath( $router->getLocale() ) );
		}
		

		Marketing_ConversionSourceDetector::performDetection();
	}

}