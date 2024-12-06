<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Complaint\Returned;

use JetApplication\Complaint_Event_HandlerModule;

/**
 *
 */
class Main extends Complaint_Event_HandlerModule
{
	
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	public function handleInternals(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Returned';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}