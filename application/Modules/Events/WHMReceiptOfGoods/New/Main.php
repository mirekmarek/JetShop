<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\WHMReceiptOfGoods\New;

use JetApplication\WarehouseManagement_ReceiptOfGoods_Event_HandlerModule;

class Main extends WarehouseManagement_ReceiptOfGoods_Event_HandlerModule
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
		return 'Created';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-new';
	}
}