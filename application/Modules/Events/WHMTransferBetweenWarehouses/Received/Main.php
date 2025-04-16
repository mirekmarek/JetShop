<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\WHMTransferBetweenWarehouses\Received;

use JetApplication\WarehouseManagement;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Event_HandlerModule;

class Main extends WarehouseManagement_TransferBetweenWarehouses_Event_HandlerModule
{
	
	public function handleExternals(): bool
	{
		return true;
	}

	public function handleInternals(): bool
	{
		WarehouseManagement::manageTransferBetweenWarehousesReceived( $this->transfer );
		
		return true;
	}
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Received';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}