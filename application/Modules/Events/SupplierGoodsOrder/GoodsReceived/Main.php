<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\SupplierGoodsOrder\GoodsReceived;

use JetApplication\Supplier_GoodsOrder_Event_HandlerModule;

class Main extends Supplier_GoodsOrder_Event_HandlerModule
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
		return 'Goods received';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-done-success';
	}
}