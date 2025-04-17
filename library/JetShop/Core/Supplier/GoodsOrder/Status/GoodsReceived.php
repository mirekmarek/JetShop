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
use JetApplication\Supplier_GoodsOrder_Event_GoodsReceived;
use JetApplication\Supplier_GoodsOrder_Status;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

abstract class Core_Supplier_GoodsOrder_Status_GoodsReceived extends Supplier_GoodsOrder_Status
{
	
	public const CODE = 'goods_received';
	
	protected bool $cancel_allowed = false;
	
	protected bool $goods_received = true;
	
	protected ?WarehouseManagement_ReceiptOfGoods $rcp = null;
	
	public function __construct()
	{
		$this->title = Tr::_('Goods received', dictionary: Tr::COMMON_DICTIONARY);
		$this->priority = 70;
	}
	
	public function getShowAdminCSSClass() : string
	{
		return 'status-done';
	}
	
	public function getPossibleFutureStatuses(): array
	{
		return [];
	}
	
	public function getReceiptOfGoods(): ?WarehouseManagement_ReceiptOfGoods
	{
		return $this->rcp;
	}
	
	public function setReceiptOfGoods( ?WarehouseManagement_ReceiptOfGoods $rcp ): void
	{
		$this->rcp = $rcp;
	}
	
	
	public function createEvent( EShopEntity_Basic|Supplier_GoodsOrder $item, EShopEntity_Status|Supplier_GoodsOrder_Status $previouse_status ): ?EShopEntity_Event
	{
		$event = $item->createEvent( new Supplier_GoodsOrder_Event_GoodsReceived() );
		if($this->rcp) {
			$event->setContext(
				$this->rcp
			);
		}
		
		return $event;
	}
	
}