<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\WarehouseManagement_LossOrDestruction_Status;
use JetApplication\EShopEntity_Basic;

abstract class Core_WarehouseManagement_LossOrDestruction_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = WarehouseManagement_LossOrDestruction_Status::class;
	
	protected bool $editable = false;
	
	protected static array $flags_map = [
	];
	
	protected static ?array $list = null;
	
	public function editable(): bool
	{
		return $this->editable;
	}
	
	
	
	public function createEvent( EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?EShopEntity_Event
	{
		return null;
	}
	
}