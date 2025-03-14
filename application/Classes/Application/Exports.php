<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
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
class Application_Exports
{
	public static function getBaseId() : string
	{
		return 'exports';
	}
	
	public static function getBase() : MVC_Base_Interface
	{
		return MVC::getBase( static::getBaseId() );
	}
	
	
	public static function init( MVC_Router $router ) : void
	{
		SysConf_Jet_MVC::setUseModulePages( false );
		
		Application::initErrorPages( $router );
		
		Exports::getManager()?->handleExports();
		
		Application::end();
	}
	
}