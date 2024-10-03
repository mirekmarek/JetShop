<?php
/**
 *
 * @copyright Copyright (c) 2011-2024 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\MVC;
use Jet\MVC_Base_Interface;
use Jet\MVC_Router;
use Jet\SysConf_Jet_MVC;

/**
 *
 */
class Application_Services
{
	public static function getBaseId() : string
	{
		return 'services';
	}
	
	public static function getBase() : MVC_Base_Interface
	{
		return MVC::getBase( static::getBaseId() );
	}
	
	public static function init( MVC_Router $router ) : void
	{
		SysConf_Jet_MVC::setUseModulePages( false );
		
		Application::initErrorPages( $router );
		
		SysServices::getManager()?->handleSysServices();
		
		Application::end();
	}
	
}