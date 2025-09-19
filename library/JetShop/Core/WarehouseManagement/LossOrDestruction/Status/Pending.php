<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\WarehouseManagement_LossOrDestruction_Status;

abstract class Core_WarehouseManagement_LossOrDestruction_Status_Pending extends WarehouseManagement_LossOrDestruction_Status
{
	
	public const CODE = 'pending';
	protected string $title = 'Pending';
	protected int $priority = 10;
	protected bool $editable = true;
	
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-pending';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}