<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 *
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Logger;
use Jet\Mvc_Site;
use Jet\Mvc_Router;
use Jet\Auth;

/**
 *
 */
class Application_REST
{
	/**
	 * @return string
	 */
	public static function getSiteId() : string
	{
		return 'rest';
	}

	/**
	 * @return Mvc_Site
	 */
	public static function getSite() : Mvc_Site
	{
		return Mvc_Site::get( static::getSiteId() );
	}

	/**
	 * @param Mvc_Router $router
	 */
	public static function init( Mvc_Router $router ) : void
	{
		Application::initErrorPages( $router );
		Logger::setLogger( new Logger_REST() );
		Auth::setController( new Auth_Controller_REST() );
	}

}