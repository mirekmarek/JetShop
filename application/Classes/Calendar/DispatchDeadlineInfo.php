<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\Data_DateTime;

class Calendar_DispatchDeadlineInfo {
	protected bool $is_before_deadline;
	protected string $deadline_time;
	protected int $number_of_days;
	protected Data_DateTime $deadline;
	protected Data_DateTime $delivery_term;
	
	public function __construct( bool $is_before_deadline, string $deadline_time, int $number_of_days, Data_DateTime $delivery_term )
	{
		$this->is_before_deadline = $is_before_deadline;
		$this->deadline_time = $deadline_time;
		$this->number_of_days = $number_of_days;
		
		if($is_before_deadline) {
			$this->deadline = new Data_DateTime(date('Y-m-d '.$deadline_time.':00'));
		} else {
			$this->deadline = new Data_DateTime(date('Y-m-d 23:59:59'));
		}
		$this->delivery_term = $delivery_term;
		
	}
	
	public function getIsBeforeDeadline(): bool
	{
		return $this->is_before_deadline;
	}
	
	public function getDeadlineTime(): string
	{
		return $this->deadline_time;
	}
	
	public function getNumberOfDays(): int
	{
		return $this->number_of_days;
	}
	
	public function getDeadline(): Data_DateTime
	{
		return $this->deadline;
	}
	
	public function getDeliveryTerm(): Data_DateTime
	{
		return $this->delivery_term;
	}
	
	
	
}