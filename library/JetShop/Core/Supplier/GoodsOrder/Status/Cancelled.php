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
use JetApplication\Supplier_GoodsOrder;
use JetApplication\Supplier_GoodsOrder_Event_Cancel;
use JetApplication\Supplier_GoodsOrder_Status;

abstract class Core_Supplier_GoodsOrder_Status_Cancelled extends Supplier_GoodsOrder_Status
{
	
	public const CODE = 'cancelled';
	
	protected bool $cancel_allowed = false;
	
	
	public function __construct()
	{
		$this->title = Tr::_('Cancelled', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 80;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-cancelled';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public function createEvent( EShopEntity_Basic|Supplier_GoodsOrder $item, EShopEntity_Status|Supplier_GoodsOrder_Status $previouse_status ): ?EShopEntity_Event
	{
		return $item->createEvent( new Supplier_GoodsOrder_Event_Cancel() );
	}
	
}