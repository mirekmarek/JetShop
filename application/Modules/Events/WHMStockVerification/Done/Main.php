<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\WHMStockVerification\Done;

use JetApplication\WarehouseManagement;
use JetApplication\WarehouseManagement_StockVerification_Event_HandlerModule;

class Main extends WarehouseManagement_StockVerification_Event_HandlerModule
{
	
	public function handleInternals(): bool
	{
		WarehouseManagement::manageStockVerification( $this->verification );
		
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Done';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}