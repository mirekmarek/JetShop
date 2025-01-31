<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\CalendarManager;


use Jet\Data_DateTime;
use JetApplication\EShop;
use DateInterval;

class Calendar {
	
	protected static array $calendars = [];
	
	protected EShop $eshop;
	protected Config_PerShop $config;
	
	
	protected array $non_working_days_of_week;
	protected array $national_holidays;
	protected array $custom_free_days;
	
	protected array $holydays;
	
	protected function __construct(  Config_PerShop $config )
	{
		$this->eshop = $config->getEshop();
		$this->config = $config;
		
		$this->non_working_days_of_week = $config->getNonWorkingDaysOfWeek();
		$this->national_holidays = $config->getNationalHolidays();
		$this->custom_free_days = $config->getCustomFreeDays();
		
		
		$this->initHolydays();
	}
	
	protected function initHolydays(): void
	{
		$start_year = (int)date('Y');
		$end_year = $start_year+2;
		
		
		$this->holydays = [];
		
		if( $this->config->hasEaster() ) {
			
			if($this->config->isJulian()) {
				$mode = CAL_EASTER_ALWAYS_JULIAN;
			} else {
				$mode = CAL_EASTER_ALWAYS_GREGORIAN;
			}
			
			for( $y=$start_year;$y<=$end_year;$y++ ) {
				$easter_sunday_date = date('Y-m-d', easter_date( $y, $mode ));
				
				if( $this->config->EasterWednesdayIsNonworkingDay() ) {
					$this->holydays[] = date('Y-m-d', strtotime( $easter_sunday_date.' -4 days' )  );
				}
				
				if( $this->config->EasterThursdayIsNonworkingDay() ) {
					$this->holydays[] = date('Y-m-d', strtotime( $easter_sunday_date.' -3 days' )  );
				}
				
				if( $this->config->EasterFridayIsNonworkingDay() ) {
					$this->holydays[] = date('Y-m-d', strtotime( $easter_sunday_date.' -2 days' )  );
				}
				
				if( $this->config->EasterMondayIsNonworkingDay() ) {
					$this->holydays[] = date('Y-m-d', strtotime( $easter_sunday_date.' +1 days' )  );
				}
				
				
			}
			
			
		}
		
		
		for( $y=$start_year;$y<=$end_year;$y++ ) {
			foreach( $this->national_holidays as $free_day ) {
				$this->holydays[] = $y.'-'.$free_day;
			}
		}
		
		foreach( $this->custom_free_days as $day ) {
			$this->holydays[] = $day;
		}
		
	}
	
	public static function get( Config_PerShop $config ) : static
	{
		$eshop = $config->getEshop();
		
		$key = $eshop->getKey();
		
		if(!isset(static::$calendars[$key])) {
			static::$calendars[$key] = new static( $config );
		}
		
		return static::$calendars[$key];
	}
	
	
	public function getHolydays() : array
	{
		return $this->holydays;
	}
	
	
	public function getNextBusinessDate( int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true  ) : Data_DateTime
	{
		$days = $this->calcBusinessDaysOffset( $number_of_working_days, $start_date, $use_holydays );
		
		return new Data_DateTime( date('Y-m-d', strtotime($start_date.' +'.$days.' days')) );
	}
	
	public function getPrevBusinessDate( int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true  ) : Data_DateTime
	{
		$days = $this->calcBusinessDaysSubset( $number_of_working_days, $start_date, $use_holydays );
		
		return new Data_DateTime( date('Y-m-d', strtotime($start_date.' -'.$days.' days')) );
	}
	
	
	public function calcBusinessDaysOffset( int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int
	{
		if($number_of_working_days<1) {
			return 0;
		}
		
		if( $use_holydays ) {
			$holydays = $this->getHolydays();
		} else {
			$holydays = [];
		}
		if(is_object($start_date)) {
			$start_date = $start_date->format('Y-m-d');
		}
		
		$timestamp = strtotime( $start_date );
		
		$working_days_added = 0;
		$days_added = 0;
		
		
		do {
			$timestamp += 86400;
			
			$days_added++;
			if(
				!in_array( date('w', $timestamp), $this->non_working_days_of_week ) &&
				!in_array( date('Y-m-d', $timestamp), $holydays)
			) {
				$working_days_added++;
				if($working_days_added>=$number_of_working_days) {
					break;
				}
			}
			
		} while( true );
		
		return $days_added;
	}
	
	
	public function calcBusinessDaysSubset( int $number_of_working_days, string|Data_DateTime $start_date='now', bool $use_holydays=true ) : int
	{
		if($number_of_working_days<1) {
			return 0;
		}
		
		if( $use_holydays ) {
			$holydays = $this->getHolydays();
		} else {
			$holydays = [];
		}
		if(is_object($start_date)) {
			$start_date = $start_date->format('Y-m-d');
		}
		
		$timestamp = strtotime( $start_date );
		
		$working_days_subtracted = 0;
		$days_subtracted = 0;
		
		do {
			$timestamp -= 86400;
			
			$days_subtracted++;
			
			if(
				!in_array( date('w', $timestamp), $this->non_working_days_of_week ) &&
				!in_array( date('Y-m-d', $timestamp), $holydays)
			) {
				$working_days_subtracted++;
				if($working_days_subtracted>=$number_of_working_days) {
					break;
				}
			}
			
		} while( true );

		
		
		return $days_subtracted;
	}
	
	
	
	public function howManyWorkingDays( Data_DateTime|string $from, Data_DateTime|string $till, bool $use_holydays=true ) : int
	{
		if( $use_holydays ) {
			$holydays = $this->getHolydays();
		} else {
			$holydays = [];
		}
		
		$interval = new DateInterval('P0D');
		
		$days_added = 0;
		
		$from = Data_DateTime::catchDateTime( $from );
		$from->setTime(0,0,0);
		
		$till = Data_DateTime::catchDateTime( $till );
		$till->setTime(0,0,0);
		
		
		if($from==$till) {
			return 0;
		}
		
		do {
			do {
				$is_working_day = true;
				$interval->d++;
				
				$date = clone $from;
				$date->add( $interval );
				
				$week_day = $date->format('w');
				
				if(
					in_array( $week_day, $this->non_working_days_of_week ) ||
					in_array( $date->format('Y-m-d'), $holydays )
				) {
					$is_working_day = false;
				}
				
			} while(!$is_working_day);
			
			$days_added++;
			
		} while($date<$till);
		
		return $days_added;
		
	}
	
	
	
}