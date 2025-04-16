<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\Event_HandlerModule;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Event;


abstract class Core_WarehouseManagement_TransferBetweenWarehouses_Event_HandlerModule extends Event_HandlerModule
{
	protected WarehouseManagement_TransferBetweenWarehouses_Event $event;
	protected WarehouseManagement_TransferBetweenWarehouses $transfer;
	
	
	public function init( EShopEntity_Event|WarehouseManagement_TransferBetweenWarehouses_Event $event ) : void
	{
		$this->event = $event;
		$this->transfer = $event->getTransfare();
	}
	
	public function getEvent(): WarehouseManagement_TransferBetweenWarehouses_Event
	{
		return $this->event;
	}
	
	public function getTransfer(): WarehouseManagement_TransferBetweenWarehouses
	{
		return $this->transfer;
	}
	
}