<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Stats\Orders;

use IntlCalendar;
use Closure;
use Jet\Locale;
use Jet\Tr;
use JetApplication\EShop;
use JetApplication\Order;
use JetApplication\Order_Status;

class Stat {
	public const KEY = null;
	
	protected int $current_month = 0;
	protected int $start_year = 2024;
	protected int $end_year = 2099;
	
	protected string $date_column = 'date_purchased';
	protected string $amount_column = 'total_amount_with_VAT';
	
	protected string $title = '';
	protected bool $is_selected = false;
	protected bool $is_default = false;
	protected bool $display_days_by_default = false;
	
	protected EShop $eshop;
	
	protected bool $display_days = false;
	
	protected array $week_days = [];
	
	protected ?Closure $getWeekDay = null;
	
	/**
	 * @var Order_Status[]
	 */
	protected array $order_statuses = [];
	
	protected array $where = [];
	
	/**
	 * @var Result[]
	 */
	protected ?array $results = null;
	
	
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
	
	
	public function getStartYear(): int
	{
		return $this->start_year;
	}
	
	public function setStartYear( int $start_year ): void
	{
		$this->start_year = $start_year;
	}
	
	public function getEndYear(): int
	{
		return $this->end_year;
	}
	
	public function setEndYear( int $end_year ): void
	{
		$this->end_year = $end_year;
	}
	
	public function getCurrentMonth(): int
	{
		return $this->current_month;
	}
	
	public function setCurrentMonth( int $current_month ): void
	{
		$this->current_month = $current_month;
	}
	
	public function getDateColumn(): string
	{
		return $this->date_column;
	}
	
	public function setDateColumn( string $date_column ): void
	{
		$this->date_column = $date_column;
	}
	
	public function getAmountColumn(): string
	{
		return $this->amount_column;
	}
	
	public function setAmountColumn( string $amount_column ): void
	{
		$this->amount_column = $amount_column;
	}
	
	
	protected function prepareResults() : void
	{
		$result = new Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		//$result->setTitle( $this->getTitle() );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
	}
	
	/**
	 * @return Result[]
	 */
	public function getResults() : array
	{
		if(!$this->results) {
			$this->results = [];
			
			$this->prepareResults();
		}
		
		return $this->results;
	}
	
	public function renderDetailFilterForm() : string
	{
		return '';
	}
	
	public function catchDetailFilterForm() : void
	{
	
	}
	
	/**
	 * @return Order_Status[]
	 */
	public function getOrderStatuses(): array
	{
		
		return $this->order_statuses;
	}
	
	/**
	 * @param array $codes
	 * @return void
	 */
	public function setOrderStatuses( array $codes ): void
	{
		$res = [];
		
		foreach($codes as $code) {
			$res[$code] = Order_Status::get( $code );
		}
		
		
		$this->order_statuses = $res;
	}
	
	public function getWhere() : array
	{
		return $this->where;
	}
	

	public function setWhere( array $where ) : void
	{
		$this->where = $where;
	}
	
	
	
	public function getDataWhere() : array
	{
		$where = $this->eshop->getWhere();
		
		$staus_where = [];
		foreach($this->getOrderStatuses() as $status) {
			if($staus_where) {
				$staus_where[] = 'OR';
			}
			$staus_where[] = $status::getStatusQueryWhere();
		}
		
		if($staus_where) {
			$where[] = 'AND';
			$where[] = $staus_where;
		}
		
		
		if( $this->getWhere() ) {
			$where[] = 'AND';
			$where[] = $this->getWhere();
		}
		
		return $where;
	}
	
	public function getRawData() : array
	{
		$data = Order::dataFetchAll(
			select: [
				$this->date_column,
				$this->amount_column,
			],
			where: $this->getDataWhere(),
			raw_mode: true
		);
		
		return $this->prepareRawData( $data );
	}
	
	protected function prepareRawData( array $data ) : array
	{
		$res = [];
		
		foreach($data as $d) {
			$day = date('Y-m-d', strtotime($d[$this->date_column]));
			if(!isset($res[$day])) {
				$res[$day] = [
					'day' => $day,
					'count' => 0,
					'amount' => 0,
				];
			}
			
			$res[$day]['count']++;
			$res[$day]['amount'] += $d[$this->amount_column];
		}
		
		return $res;
	}
	

}