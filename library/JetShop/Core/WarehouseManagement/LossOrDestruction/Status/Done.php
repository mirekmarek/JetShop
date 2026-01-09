<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Status_PossibleFutureStatus;
use JetApplication\WarehouseManagement_LossOrDestruction_Status;

abstract class Core_WarehouseManagement_LossOrDestruction_Status_Done extends WarehouseManagement_LossOrDestruction_Status {
	
	public const CODE = 'done';
	protected string $title = 'Done';
	protected int $priority = 50;
	
	protected bool $cancel_allowed = false;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public static function getAsPossibleFutureStatus(): ?EShopEntity_Status_PossibleFutureStatus
	{
		return null;
	}
}