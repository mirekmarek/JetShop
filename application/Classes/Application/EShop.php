<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use Jet\Exception;
use Jet\Logger;

use Jet\MVC_Router;

use Jet\Auth;
use Jet\SysConf_Jet_ErrorPages;
use Jet\SysConf_Jet_Form;
use Jet\SysConf_Jet_MVC;
use Jet\SysConf_Jet_UI;

/**
 *
 */
class Application_EShop
{

	/**
	 * @param MVC_Router $router
	 */
	public static function init( MVC_Router $router ) : void
	{
		SysConf_Jet_MVC::setUseModulePages( false );
		
		Application::initErrorPages( $router );
		Logger::setLogger( new Logger_EShop() );
		Auth::setController( new Customer_AuthController() );

		if(!EShops::determineByBase( $router->getBase()->getId(), $router->getLocale() )) {
			throw new Exception('Unknown e-shop');
		}
		
		$eshop = EShops::getCurrent();

		if($eshop->getUseTemplate()) {
			$template = $eshop->getTemplate();
			
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
		EShop_Managers::Analytics()?->catchConversionSourceInfo();
	}

}