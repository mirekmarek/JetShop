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
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_StockVerification_Event_Done;
use JetApplication\WarehouseManagement_StockVerification_Status;

abstract class Core_WarehouseManagement_StockVerification_Status_Done extends WarehouseManagement_StockVerification_Status
{
	
	public const CODE = 'done';
	
	
	
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
	
	
	
	public function createEvent(
		EShopEntity_Basic|WarehouseManagement_StockVerification $item,
		EShopEntity_Status|WarehouseManagement_StockVerification_Status $previouse_status
	): ?EShopEntity_Event
	{
		return $item->createEvent( new WarehouseManagement_StockVerification_Event_Done() );
	}
	
}