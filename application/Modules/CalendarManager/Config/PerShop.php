<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\CalendarManager;



use Jet\Config_Definition;
use Jet\Form_Definition;
use Jet\Form_Definition_Interface;
use Jet\Form_Definition_Trait;
use Jet\Form_Field;
use Jet\Tr;
use JetApplication\EShopConfig_ModuleConfig_PerShop;
use Jet\Config;

#[Config_Definition(
	name: 'Calendar'
)]
class Config_PerShop extends EShopConfig_ModuleConfig_PerShop implements Form_Definition_Interface {
	use Form_Definition_Trait;
	
	protected static array $default_national_holidays = [
		'cs_CZ' => ['01-01', '05-01', '05-08', '07-05', '07-06', '09-28', '10-28', '11-17', '12-24', '12-25', '12-26',],
		'sk_SK' => ['01-01', '01-06', '05-01', '05-08', '07-05', '08-05', '09-01', '09-15', '11-01', '11-17', '12-24', '12-25', '12-26'],
	];
	
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is julian',
	)]
	protected bool $is_julian = false;
	
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Has Easter',
	)]
	protected bool $has_easter = true;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Easter Wednesday is a non-working day',
	)]
	protected bool $easter_wednesday_is_nonworking_day = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Easter Thursday is a non-working day',
	)]
	protected bool $easter_thursday_is_nonworking_day = false;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Easter Friday is a non-working day',
	)]
	protected bool $easter_friday_is_nonworking_day = true;
	
	#[Config_Definition(
		type: Config::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Easter Monday is a non-working day',
	)]
	protected bool $easter_monday_is_nonworking_day = true;
	
	
	#[Config_Definition(
		type: Config::TYPE_ARRAY,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Non-working days of week:',
		select_options_creator: [
			self::class,
			'getWeekdays'
		]
	)]
	protected array $non_working_days_of_week = [0, 6];
	
	
	#[Config_Definition(
		type: Config::TYPE_STRING,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TIME,
		label: 'Order deadline time:',
	)]
	protected string $order_deadline_time = '';
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Number of days required for dispatch - before deadline:',
	)]
	protected int $number_of_days_required_for_dispatch_before_order_deadline = 1;
	
	#[Config_Definition(
		type: Config::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Number of days required for dispatch - after deadline:',
	)]
	protected int $number_of_days_required_for_dispatch_after_order_deadline = 2;
	
	
	
	#[Config_Definition(
		type: Config::TYPE_ARRAY,
	)]
	protected array $national_holidays = [];
	
	#[Config_Definition(
		type: Config::TYPE_ARRAY,
	)]
	protected array $custom_free_days = [];
	
	
	public static function getWeekdays() : array
	{
		return [
			1 => Tr::_('Monday'),
			2 => Tr::_('Tuesday'),
			3 => Tr::_('Wednesday'),
			4 => Tr::_('Thursday'),
			5 => Tr::_('Friday'),
			6 => Tr::_('Saturday'),
			0 => Tr::_('Sunday'),
		];
	}
	
	
	public function isJulian(): bool
	{
		return $this->is_julian;
	}
	
	public function setIsJulian( bool $is_julian ): void
	{
		$this->is_julian = $is_julian;
	}
	
	public function hasEaster(): bool
	{
		return $this->has_easter;
	}
	
	public function setHasEaster( bool $has_easter ): void
	{
		$this->has_easter = $has_easter;
	}
	
	public function getNationalHolidays(): array
	{
		if(
			!$this->national_holidays &&
			isset( static::$default_national_holidays[$this->eshop->getLocale()->toString()])
		) {
			$this->national_holidays = static::$default_national_holidays[$this->eshop->getLocale()->toString()];
			$this->saveConfigFile();
		}
		
		return $this->national_holidays;
	}
	
	public function setNationalHolidays( array $national_holidays ): void
	{
		$this->national_holidays = $national_holidays;
	}
	
	public function getCustomFreeDays(): array
	{
		return $this->custom_free_days;
	}
	
	public function setCustomFreeDays( array $custom_free_days ): void
	{
		$this->custom_free_days = $custom_free_days;
	}
	
	public function getNonWorkingDaysOfWeek(): array
	{
		return $this->non_working_days_of_week;
	}
	
	public function setNonWorkingDaysOfWeek( array $non_working_days_of_week ): void
	{
		$this->non_working_days_of_week = $non_working_days_of_week;
	}
	
	public function EasterWednesdayIsNonworkingDay(): bool
	{
		return $this->easter_wednesday_is_nonworking_day;
	}
	
	public function setEasterWednesdayIsNonworkingDay( bool $easter_wednesday_is_nonworking_day ): void
	{
		$this->easter_wednesday_is_nonworking_day = $easter_wednesday_is_nonworking_day;
	}
	
	public function EasterThursdayIsNonworkingDay(): bool
	{
		return $this->easter_thursday_is_nonworking_day;
	}
	
	public function setEasterThursdayIsNonworkingDay( bool $easter_thursday_is_nonworking_day ): void
	{
		$this->easter_thursday_is_nonworking_day = $easter_thursday_is_nonworking_day;
	}
	
	public function EasterFridayIsNonworkingDay(): bool
	{
		return $this->easter_friday_is_nonworking_day;
	}
	
	public function setEasterFridayIsNonworkingDay( bool $easter_friday_is_nonworking_day ): void
	{
		$this->easter_friday_is_nonworking_day = $easter_friday_is_nonworking_day;
	}
	
	public function EasterMondayIsNonworkingDay(): bool
	{
		return $this->easter_monday_is_nonworking_day;
	}
	
	public function setEasterMondayIsNonworkingDay( bool $easter_monday_is_nonworking_day ): void
	{
		$this->easter_monday_is_nonworking_day = $easter_monday_is_nonworking_day;
	}
	
	public function addNationalHoliday( string $day ) : bool
	{
		$day = strtotime( $day );
		if(!$day) {
			return false;
		}
		
		$day = date('m-d', $day);
		if(!in_array($day, $this->national_holidays)) {
			$this->national_holidays[] = $day;
			
			return true;
		}
		
		return false;
	}
	
	public function removeNationalHoliday( string $day ) : bool
	{
		
		$day = strtotime( date('Y').'-'.$day );
		if(!$day) {
			return false;
		}
		
		$day = date('m-d', $day);
		
		foreach($this->national_holidays as $k=>$v) {
			if($day==$v) {
				unset($this->national_holidays[$k]);
				break;
			}
		}
		
		$this->national_holidays = array_values( $this->national_holidays );
		
		return true;
	}
	
	public function addCustomFreeDay( string $day ) : bool
	{
		$day = strtotime( $day );
		if(!$day) {
			return false;
		}
		$day = date('Y-m-d', $day);
		
		
		if(!in_array($day, $this->custom_free_days)) {
			$this->custom_free_days[] = $day;
			
			return true;
		}
		
		return false;
	}
	
	public function removeCustomFreeDay( string $day ) : bool
	{
		$day = strtotime( $day );
		if(!$day) {
			return false;
		}
		
		$day = date('Y-m-d', $day);
		foreach($this->custom_free_days as $k=>$v) {
			if($day==$v) {
				unset($this->custom_free_days[$k]);
				break;
			}
		}
		
		$this->custom_free_days = array_values( $this->custom_free_days );
		asort( $this->custom_free_days );
		
		return true;
	}
	
	public function getOrderDeadlineTime(): string
	{
		return $this->order_deadline_time;
	}
	
	public function setOrderDeadlineTime( string $order_deadline_time ): void
	{
		$this->order_deadline_time = $order_deadline_time;
	}
	
	public function getNumberOfDaysRequiredForDispatchBeforeOrderDeadline(): int
	{
		return $this->number_of_days_required_for_dispatch_before_order_deadline;
	}
	
	public function setNumberOfDaysRequiredForDispatchBeforeOrderDeadline( int $number_of_days_required_for_dispatch_before_order_deadline ): void
	{
		$this->number_of_days_required_for_dispatch_before_order_deadline = $number_of_days_required_for_dispatch_before_order_deadline;
	}
	
	public function getNumberOfDaysRequiredForDispatchAfterOrderDeadline(): int
	{
		return $this->number_of_days_required_for_dispatch_after_order_deadline;
	}
	
	public function setNumberOfDaysRequiredForDispatchAfterOrderDeadline( int $number_of_days_required_for_dispatch_after_order_deadline ): void
	{
		$this->number_of_days_required_for_dispatch_after_order_deadline = $number_of_days_required_for_dispatch_after_order_deadline;
	}
	
	
	
}