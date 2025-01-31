<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\BaseObject;
use JetApplication\Statistics_Order;
use JetApplication\Statistics_Order_Result;
use JetApplication\Statistics_Order_Result_Item;

class Core_Statistics_Order_Result extends BaseObject {
	
	protected Statistics_Order $stat;
	protected string $title = '';
	protected int $current_month = 0;
	protected int $start_year = 2024;
	protected int $end_year = 2099;
	protected array $day_map = [];
	
	/**
	 * @var Statistics_Order_Result_Item[][][]
	 */
	protected ?array $data_by_day = null;
	
	/**
	 * @var Statistics_Order_Result_Item[][]
	 */
	protected ?array $data_by_months = null;
	
	/**
	 * @var Statistics_Order_Result_Item[]
	 */
	protected ?array $data_by_years = null;
	
	
	/**
	 * @var Statistics_Order_Result_Item[]
	 */
	protected ?array $data_by_completed_part_of_year = null;
	
	public function __construct( Statistics_Order $stat, int $start_year, int $end_year, int $current_month )
	{
		$this->stat = $stat;
		
		$this->start_year = $start_year;
		$this->end_year = $end_year;
		
		$this->current_month = $current_month;
		
		$this->prepareMap();
	}
	
	public function getStat() : Statistics_Order
	{
		return $this->stat;
	}
	
	public function getDayMap() : array
	{
		return $this->day_map;
	}
	
	public function getTitle() : string
	{
		return $this->title;
	}
	
	public function setTitle( string $title ) : void
 	{
		$this->title = $title;
	}
	
	
	public function getStartYear() : int
	{
		return $this->start_year;
	}
	
	public function setStartYear( int $start_year ) : void
	{
		$this->start_year = $start_year;
	}
	
	public function getEndYear() : int
	{
		return $this->end_year;
	}
	
	public function setEndYear( int $end_year ) : void
	{
		$this->end_year = $end_year;
	}
	
	public function getCurrentMonth() : int
	{
		return $this->current_month;
	}
	
	public function setCurrentMonth( int $current_month ) : void
	{
		$this->current_month = $current_month;
	}
	
	
	public function prepareMap() : void 
	{
		/**
		 * @var Statistics_Order_Result $this
		 */
		
		$this->day_map = [];
		
		$this->data_by_day = [];
		$this->data_by_months = [];
		$this->data_by_years = [];
		
		$this_y = (int)date('Y');
		$this_m = (int)date('m');
		$this_d = date('d');
		
		for( $y=$this->start_year; $y<=$this->end_year; $y++ ) {
			$this->day_map[$y] = [];
			
			$this->data_by_day[$y] = [];
			$this->data_by_months[$y] = [];
			
			$this->data_by_years[$y] = new Statistics_Order_Result_Item( $this );
			$this->data_by_completed_part_of_year[$y] = new Statistics_Order_Result_Item( $this );
			
			$year_day_count = 0;
			$period_day_count = 0;
			
			for( $m=1; $m<=12; $m++ ) {
				$this->day_map[$y][$m] = [];
				
				$this->data_by_day[$y][$m] = [];
				$this->data_by_months[$y][$m] = new Statistics_Order_Result_Item( $this );
				
				$month_day_count = 31;
				if($m==2) {
					if( ((($y % 4) == 0) && ((($y % 100) != 0) || (($y %400) == 0))) ) {
						$month_day_count = 29;
					} else {
						$month_day_count = 28;
					}
				}
				if(in_array($m, [4,6,9,11])) {
					$month_day_count = 30;
				}
				
				
				
				
				for($d=1; $d<=$month_day_count;$d++) {
					$this->day_map[$y][$m][] = $d;
					
					$this->data_by_day[$y][$m][$d] = new Statistics_Order_Result_Item( $this );
				}
				
				
				if($y==$this_y) {
					
					if($m==$this_m) {
						$month_day_count = $this_d;
						
						foreach(array_keys($this->data_by_day[$y][$m]) as $f_day ) {
							if($f_day>$this_d) {
								$this->data_by_day[$y][$m][$f_day] = null;
							}
						}
					}
					
					if($m>$this_m) {
						$month_day_count = 0;
						foreach(array_keys($this->data_by_day[$y][$m]) as $f_day ) {
							$this->data_by_day[$y][$m][$f_day] = null;
						}
					}
				}
				
				
				$year_day_count = $year_day_count + $month_day_count;
				
				if($m<$this->current_month) {
					$period_day_count = $period_day_count + $month_day_count;
				}
				
				$this->data_by_months[$y][$m]->setAverageDivisor($month_day_count);
			}
			
			$this->data_by_years[$y]->setAverageDivisor( $year_day_count );
			$this->data_by_completed_part_of_year[$y]->setAverageDivisor( $period_day_count );
		}
	}
	
	
	public function setData( array $data ) : void
	{
		foreach( $data as $i ) {
			[$y, $m, $d] = explode('-', $i['day']);
			if(!isset($this->data_by_years[$y])) {
				continue;
			}
			
			$count = $i['count'];
			$amount = $i['amount'];
			
			$y = (int)$y;
			$m = (int)$m;
			$d = (int)$d;
			
			$this->data_by_day[$y][$m][$d]->append( $count, $amount );
			$this->data_by_months[$y][$m]->append( $count, $amount );
			$this->data_by_years[$y]->append( $count, $amount );
			
			if($m<$this->current_month) {
				$this->data_by_completed_part_of_year[$y]->append( $count, $amount );
			}
		}
		
	}
	
	/**
	 * @return Statistics_Order_Result_Item[][][]
	 */
	public function getDataByDay() : array
	{
		return $this->data_by_day;
	}
	
	public function getDayData( int $y, int $m, int $d ) : ?Statistics_Order_Result_Item
	{
		$data = $this->getDataByDay();
		if( !isset($data[$y][$m][$d]) ) {
			return null;
		}
		
		return $data[$y][$m][$d];
	}
	
	/**
	 * @return Statistics_Order_Result_Item[][]
	 */
	public function getDataByMonths() : array
	{
		return $this->data_by_months;
	}
	
	public function getMonthData( int $y, int $m ) : Statistics_Order_Result_Item
	{
		return $this->getDataByMonths()[$y][$m];
	}
	
	/**
	 * @return Statistics_Order_Result_Item[]
	 */
	public function getDataByYears() : array
	{
		return $this->data_by_years;
	}
	
	public function getYearData( int $y ) : Statistics_Order_Result_Item
	{
		return $this->getDataByYears()[$y];
	}
	
	
	/**
	 * @return Statistics_Order_Result_Item[]
	 */
	public function getDataByCompletedPartsOfYear() : array
	{
		return $this->data_by_completed_part_of_year;
	}
	
	public function getCompletedPartOfYear( int $y ) :  Statistics_Order_Result_Item
	{
		return $this->getDataByCompletedPartsOfYear()[$y];
	}
	
	
}