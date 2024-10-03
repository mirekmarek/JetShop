<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Calendar_Manager;
use JetApplication\Managers_General;
use JetApplication\Shops_Shop;

abstract class Core_Calendar {
	
	public static function getManager() : Calendar_Manager|Application_Module
	{
		return Managers_General::Calendar();
	}
	
	
	public static function getNextBusinessDate( ?Shops_Shop $shop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime
	{
		return static::getManager()->getNextBusinessDate( $shop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function getPrevBusinessDate( ?Shops_Shop $shop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime
	{
		return static::getManager()->getPrevBusinessDate( $shop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function calcBusinessDaysOffset( Shops_Shop $shop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int
	{
		return static::getManager()->calcBusinessDaysOffset( $shop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function calcBusinessDaysSubset( Shops_Shop $shop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int
	{
		return static::getManager()->calcBusinessDaysSubset( $shop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function howManyWorkingDays( Shops_Shop $shop, Data_DateTime|string $from, Data_DateTime|string $till, bool$use_holydays=true ) : int
	{
		return static::getManager()->howManyWorkingDays( $shop, $from, $till, $use_holydays );
	}
	
}