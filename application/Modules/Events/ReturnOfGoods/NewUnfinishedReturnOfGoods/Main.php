<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\ReturnOfGoods\NewUnfinishedReturnOfGoods;

use JetApplication\ReturnOfGoods_Event_HandlerModule;

/**
 *
 */
class Main extends ReturnOfGoods_Event_HandlerModule
{
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'New unfinished return of goods';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}