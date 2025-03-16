<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Stats\Orders;

use Jet\Locale;

class Result_Item {
	
	protected Result $result;
	protected int $orders_count = 0;
	protected float $amount = 0.0;
	protected int $average_divisor = 0;
	protected ?Result_Item $ratio_item = null;
	
	public function __construct( Result $result)
	{
		$this->result = $result;
	}
	
	public function getAverageDivisor() : int
	{
		return $this->average_divisor;
	}
	
	public function setAverageDivisor( int $average_divisor ) : void
	{
		$this->average_divisor = $average_divisor;
	}
	
	public function getRatioItem() : ?Result_Item
	{
		return $this->ratio_item;
	}
	
	public function setRatioItem( Result_Item $ratio_item ) : void
	{
		$this->ratio_item = $ratio_item;
	}
	
	public function set( int $orders_count, float $amount ) : void
	{
		
		$this->orders_count = $orders_count;
		$this->amount = $amount;
		
	}
	
	public function append( int $orders_count, float $amount ) : void
	{
		
		$this->orders_count = $this->orders_count + $orders_count;
		$this->amount = $this->amount + $amount;
		
	}
	
	public function getOrdersCount( bool $format=true ) : int|string
	{
		if(!$format) {
			return $this->orders_count;
		}
		
		return Locale::int( $this->orders_count );
	}
	
	public function getAverageOrdersCount( bool $format=true ) : int|string
	{
		if($this->average_divisor<1) {
			return 0;
		}
		
		$avg = round( $this->orders_count / $this->average_divisor );
		
		if(!$format) {
			return $avg;
		}
		
		return Locale::int( $avg );
		
	}
	
	public function getOrdersCountRatio( bool $format=true ) : float|string
	{
		$d_count = $this->ratio_item->getOrdersCount(false);
		
		if(!$d_count) {
			$ratio = 0;
		} else {
			$ratio = $this->orders_count / ($d_count/100);
		}
		
		if(!$format) {
			return $ratio;
		}
		
		return Locale::float( $ratio );
	}
	
	
	public function getAmount( bool $format=true ) : float|string
	{
		if(!$format) {
			return $this->amount;
		}
		
		return Locale::float( $this->amount );
	}
	
	
	public function getAmountWithoutRecount( bool $format=true ) : float|string
	{
		if(!$format) {
			return $this->amount;
		}
		
		return Locale::float( $this->amount );
	}
	
	public function getAverageAmount( bool $format=true ) : float|string
	{
		if($this->average_divisor<1) {
			return 0;
		}
		
		$avg = $this->amount / $this->average_divisor;
		
		if(!$format) {
			return $avg;
		}
		
		return Locale::float( $avg );
		
	}
	
	public function getAmountRatio( $format=true ) : float|string
	{
		$d_count = $this->ratio_item->getAmountWithoutRecount(false);
		
		if(!$d_count) {
			$ratio = 0;
		} else {
			$ratio = $this->amount / ($d_count/100);
		}
		
		if(!$format) {
			return $ratio;
		}
		
		return Locale::float( $ratio );
	}
	
}