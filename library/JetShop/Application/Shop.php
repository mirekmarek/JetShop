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

use Jet\Mvc;
use Jet\Mvc_Router;

use Jet\Auth;
use Jet\Mvc_Site;

/**
 *
 */
class Application_Shop
{
	/**
	 * @return string
	 */
	public static function getSiteId(): string
	{
		return 'shop';
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
		Logger::setLogger( new Logger_Shop() );
		Auth::setController( new Customer_AuthController() );

		$site_id = static::getSiteId();
		$locale = Mvc::getCurrentLocale()->toString();
		foreach( Shops::getList() as $shop ) {
			if(
				$shop->getSiteId()==$site_id &&
				$shop->getLocale()->toString()==$locale
			) {
				Shops::setCurrent( $shop->getCode() );

				return;
			}
		}

		throw new Exception('Unknown shop');
	}

}