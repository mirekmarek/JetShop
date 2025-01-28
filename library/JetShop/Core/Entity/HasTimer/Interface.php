<?php
namespace JetShop;

use JetApplication\Timer_Action;

interface Core_Entity_HasTimer_Interface {
	
	/**
	 * @return Timer_Action[]
	 */
	public function getAvailableTimerActions() : array;

}