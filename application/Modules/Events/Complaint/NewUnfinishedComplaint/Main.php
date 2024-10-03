<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Events\Complaint\NewUnfinishedComplaint;

use JetApplication\Complaint_Event_HandlerModule;

/**
 *
 */
class Main extends Complaint_Event_HandlerModule
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
		return 'New unfinished complaint';
	}
	
	public function getEventStyle(): string
	{
		return '';
	}
}