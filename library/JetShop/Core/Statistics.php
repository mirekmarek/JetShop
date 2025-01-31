<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Closure;
use Jet\BaseObject;
use Jet\Tr;
use JetApplication\EShop;
use JetApplication\Statistics_Order_Result;
use IntlCalendar;
use Jet\Locale;

abstract class Core_Statistics extends BaseObject {
	
	public const KEY = null;
	
	protected string $title = '';
	protected bool $is_selected = false;
	protected bool $is_default = false;
	protected bool $display_days_by_default = false;
	
	protected EShop $eshop;
	
	protected bool $display_days = false;
	
	
	protected array $week_days = [];
	
	protected ?Closure $getWeekDay = null;
	
	public function __construct()
	{
		$this->setFirstDayOfWeek(
			IntlCalendar::createInstance(
				locale: Locale::getCurrentLocale()->toString()
			)->getFirstDayOfWeek()
		);
	}
	
	public function setFirstDayOfWeek( int $day ) : void
	{
		if($day==IntlCalendar::DOW_MONDAY) {
			$this->week_days = [
				0 => Tr::_('Mon'),
				1 => Tr::_('Tues'),
				2 => Tr::_('Wed'),
				3 => Tr::_('Thur'),
				4 => Tr::_('Fri'),
				5 => Tr::_('Sat'),
				6 => Tr::_('Sun'),
			];
			
			$this->getWeekDay = function( $date ) {
				$w = date('w', strtotime( $date ));
				
				if($w==0) {
					$w = 7;
				}
				
				$w--;
				
				return $w;
			};
			
		} else {
			$this->week_days = [
				0 => Tr::_('Sun'),
				1 => Tr::_('Mon'),
				2 => Tr::_('Tues'),
				3 => Tr::_('Wed'),
				4 => Tr::_('Thur'),
				5 => Tr::_('Fri'),
				6 => Tr::_('Sat'),
			];
			
			$this->getWeekDay = function( $date ) {
				return date('w', strtotime( $date ));
			};
		}
	}
	
	public function getWeekDayNo( int $y, int $m, int $d ) : int
	{
		$getWeekDay = $this->getWeekDay;
		return $getWeekDay($y.'-'.$m.'-'.$d);
	}
	
	public function getWeekDayName( int $y, int $m, int $d ) : string
	{
		return $this->week_days[ $this->getWeekDayNo( $y, $m, $d )  ];
	}
	
	/**
	 * @var Statistics_Order_Result[]
	 */
	protected ?array $results = null;
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function setEshop( EShop $eshop ): void
	{
		$this->eshop = $eshop;
	}
	
	
	
	
	public function getKey() : string
	{
		return static::KEY;
	}
	
	public function getTitle( bool $translate=true ) : string
	{
		if($translate) {
			return Tr::_($this->title);
		}
		
		return $this->title;
	}
	
	public function setTitle( string $title ) : void
	{
		$this->title = $title;
	}
	
	public function getIsSelected() : bool
	{
		return $this->is_selected;
	}
	
	public function setIsSelected( bool $is_selected ) : void
	{
		$this->is_selected = $is_selected;
	}
	
	public function getIsDefault() : bool
	{
		return $this->is_default;
	}
	
	public function setIsDefault( bool $is_default) : void
	{
		$this->is_default = $is_default;
	}
	
	public function getDisplayDaysByDefault(): bool
	{
		return $this->display_days_by_default;
	}
	
	public function setDisplayDaysByDefault( bool $display_days_by_default ): void
	{
		$this->display_days_by_default = $display_days_by_default;
	}
	
	public function getDisplayDays(): bool
	{
		return $this->display_days;
	}
	
	public function setDisplayDays( bool $display_days ): void
	{
		$this->display_days = $display_days;
	}
}