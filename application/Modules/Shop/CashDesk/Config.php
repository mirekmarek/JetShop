<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Shop\CashDesk;

use JetApplication\CashDesk;

class Config {

	public static function map_API_key( CashDesk $cash_desk ) : string
	{
		return 'AIzaSyAQw0jlYwFskEpdHZXALy79nslrmE49PZQ';
	}

	public static function map_center_lat( CashDesk $cash_desk ) : float {
		return 49.7437572;
	}

	public static function map_center_lon( CashDesk $cash_desk ) : float {
		return 15.3386383;
	}

	public static function map_default_zoom( CashDesk $cash_desk ) : int {
		return 8;
	}

}