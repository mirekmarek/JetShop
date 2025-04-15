<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use JetApplication\WarehouseManagement_LossOrDestruction_Status;

abstract class Core_WarehouseManagement_LossOrDestruction_Status_Done extends WarehouseManagement_LossOrDestruction_Status {
	
	public const CODE = 'done';
	
	protected bool $cancel_allowed = false;
	
	
	public function __construct()
	{
		$this->title = Tr::_('Done', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 50;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
}