<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\EShopEntity_Basic;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status;

abstract class Core_WarehouseManagement_TransferBetweenWarehouses_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = WarehouseManagement_TransferBetweenWarehouses_Status::class;
	
	
	protected static array $flags_map = [
	];
	
	protected static ?array $list = null;
	
	
	protected bool $cancel_allowed = true;
	
	protected bool $editable = false;
	
	protected bool $pending = false;
	
	protected bool $sent = false;
	
	public function editable(): bool
	{
		return $this->editable;
	}
	
	public function cancelAllowed(): bool
	{
		return $this->cancel_allowed;
	}
	
	public function pending(): bool
	{
		return $this->pending;
	}
	
	public function sent(): bool
	{
		return $this->sent;
	}
	
	
	abstract public function createEvent(
		EShopEntity_Basic|WarehouseManagement_TransferBetweenWarehouses $item,
		EShopEntity_Status|WarehouseManagement_TransferBetweenWarehouses_Status $previouse_status
	): ?EShopEntity_Event;
	
}