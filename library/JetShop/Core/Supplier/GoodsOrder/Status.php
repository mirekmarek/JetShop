<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_Status;
use JetApplication\Supplier_GoodsOrder_Status;
use JetApplication\EShopEntity_Basic;

abstract class Core_Supplier_GoodsOrder_Status extends EShopEntity_Status {
	
	protected static string $base_status_class = Supplier_GoodsOrder_Status::class;
	
	protected bool $order_can_be_updated = false;
	
	protected bool $goods_received = false;
	
	protected bool $sent_to_the_cupplier = false;
	
	protected bool $send_allowed = false;
	
	protected bool $cancel_allowed = true;
	
	
	protected static array $flags_map = [
	];
	
	protected static ?array $list = null;
	
	public function orderCanBeUpdated() : bool
	{
		return $this->order_can_be_updated;
	}
	
	public function goodsReceived(): bool
	{
		return $this->goods_received;
	}
	
	public function sentToTheCupplier(): bool
	{
		return $this->sent_to_the_cupplier;
	}
	
	public function sendAllowed(): bool
	{
		return $this->send_allowed;
	}
	
	public function cancelAllowed(): bool
	{
		return $this->cancel_allowed;
	}
	
	
	
	
	public function createEvent( EShopEntity_Basic $item, EShopEntity_Status $previouse_status ): ?EShopEntity_Event
	{
		return null;
	}
	
}