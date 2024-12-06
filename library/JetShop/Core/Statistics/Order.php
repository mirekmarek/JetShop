<?php
namespace JetShop;

use Closure;
use JetApplication\EShop;
use JetApplication\Order;
use JetApplication\Statistics;
use JetApplication\Statistics_Order;
use JetApplication\Statistics_Order_Result;
use JetApplication\Order_Status;

abstract class Core_Statistics_Order extends Statistics {
	
	protected int $current_month = 0;
	protected int $start_year = 2024;
	protected int $end_year = 2099;
	
	protected string $date_column = 'date_purchased';
	protected string $amount_column = 'total_amount_with_VAT';
	
	/**
	 * @var Order_Status[]
	 */
	protected array $order_statuses = [];
	
	protected array $where = [];
	
	protected array $week_days = [];
	
	protected ?Closure $getWeekDay = null;
	
	/**
	 * @var Statistics_Order_Result[]
	 */
	protected ?array $results = null;
	
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
	
	public function getEshop(): EShop
	{
		return $this->eshop;
	}
	
	public function setEshop( EShop $eshop ): void
	{
		$this->eshop = $eshop;
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
		/**
		 * @var Statistics_Order $this
		 */
		$result = new Statistics_Order_Result( $this,  $this->start_year, $this->end_year, $this->current_month  );
		//$result->setTitle( $this->getTitle() );
		$result->setData( $this->getRawData() );
		$this->results[] = $result;
	}
	
	/**
	 * @return Statistics_Order_Result[]
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