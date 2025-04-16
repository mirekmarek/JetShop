<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Tr;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Event_Sent;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Status;

abstract class Core_WarehouseManagement_TransferBetweenWarehouses_Status_Sent extends WarehouseManagement_TransferBetweenWarehouses_Status
{
	
	public const CODE = 'sent';
	
	protected bool $cancel_allowed = false;
	protected bool $sent = true;
	
	
	public function __construct()
	{
		$this->title = Tr::_('Sent', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 40;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-in-progress';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_TransferBetweenWarehouses $item,
		EShopEntity_Status|WarehouseManagement_TransferBetweenWarehouses_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_TransferBetweenWarehouses_Event_Sent() );
	}
	
}