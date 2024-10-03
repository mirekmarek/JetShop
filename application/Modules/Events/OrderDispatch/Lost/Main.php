<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\OrderDispatch\Lost;

use JetApplication\OrderDispatch_Event_HandlerModule;

/**
 *
 */
class Main extends OrderDispatch_Event_HandlerModule
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
		return true;
	}
	
	public function getEventStyle(): string
	{
		return true;
	}
}