<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Shop\CashDesk;

use Jet\Mvc_Controller_Default;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		$this->output('cash_desk');
	}
}