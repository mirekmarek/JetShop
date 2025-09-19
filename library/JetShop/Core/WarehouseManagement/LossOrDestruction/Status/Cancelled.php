<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\WarehouseManagement_LossOrDestruction_Status;

abstract class Core_WarehouseManagement_LossOrDestruction_Status_Cancelled extends WarehouseManagement_LossOrDestruction_Status
{
	
	public const CODE = 'cancelled';
	protected string $title = 'Cancelled';
	protected int $priority = 80;
	
	protected bool $cancel_allowed = false;
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}