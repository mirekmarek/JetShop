<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Calendar_Manager;
use JetApplication\Managers_General;
use JetApplication\EShop;

abstract class Core_Calendar {
	
	public static function getManager() : Calendar_Manager|Application_Module
	{
		return Managers_General::Calendar();
	}
	
	
	public static function getNextBusinessDate( ?EShop $eshop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime
	{
		return static::getManager()->getNextBusinessDate( $eshop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function getPrevBusinessDate( ?EShop $eshop=null, int $number_of_working_days=1, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : Data_DateTime
	{
		return static::getManager()->getPrevBusinessDate( $eshop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function calcBusinessDaysOffset( EShop $eshop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int
	{
		return static::getManager()->calcBusinessDaysOffset( $eshop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function calcBusinessDaysSubset( EShop $eshop, int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int
	{
		return static::getManager()->calcBusinessDaysSubset( $eshop, $number_of_working_days, $start_date, $use_holydays );
	}
	
	public static function howManyWorkingDays( EShop $eshop, Data_DateTime|string $from, Data_DateTime|string $till, bool $use_holydays=true ) : int
	{
		return static::getManager()->howManyWorkingDays( $eshop, $from, $till, $use_holydays );
	}
	
}