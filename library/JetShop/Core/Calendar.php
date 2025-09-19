<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Data_DateTime;
use JetApplication\Application_Service_General_Calendar;
use JetApplication\Application_Service_General;
use JetApplication\EShop;

abstract class Core_Calendar {
	
	public static function getManager() : Application_Service_General_Calendar|Application_Module
	{
		return Application_Service_General::Calendar();
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