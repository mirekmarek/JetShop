<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Events\SupplierGoodsOrder\Cancel;

use JetApplication\Supplier_GoodsOrder_Event_HandlerModule;

class Main extends Supplier_GoodsOrder_Event_HandlerModule
{

	public function handleInternals(): bool
	{
		return true;
	}
	
	public function getEventNameReadable(): string
	{
		return 'Order cancelled';
	}
	
	public function getEventCSSClass(): string
	{
		return 'event-cancel';
	}
}