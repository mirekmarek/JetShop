<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Timer_Action;

interface Core_EShopEntity_HasTimer_Interface {
	
	/**
	 * @return Timer_Action[]
	 */
	public function getAvailableTimerActions() : array;

}