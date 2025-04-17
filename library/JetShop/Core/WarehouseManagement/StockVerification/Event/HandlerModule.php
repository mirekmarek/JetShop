<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_StockVerification_Event;


abstract class Core_WarehouseManagement_StockVerification_Event_HandlerModule extends Event_HandlerModule
{
	protected WarehouseManagement_StockVerification_Event $event;
	protected WarehouseManagement_StockVerification $verification;
	
	
	public function init( EShopEntity_Event|WarehouseManagement_StockVerification_Event $event ) : void
	{
		$this->event = $event;
		$this->verification = $event->getVerification();
	}
	
	public function getEvent(): WarehouseManagement_StockVerification_Event
	{
		return $this->event;
	}
	
	public function getVerification(): WarehouseManagement_StockVerification
	{
		return $this->verification;
	}
	
	
	public function handleExternals(): bool
	{
		return true;
	}
	
	
	public function sendNotifications(): bool
	{
		return true;
	}
	
}