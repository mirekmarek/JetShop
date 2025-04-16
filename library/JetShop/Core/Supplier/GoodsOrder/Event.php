<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_Event;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\Supplier_GoodsOrder_Event;

#[DataModel_Definition(
	name: 'supplier_goods_order_event',
	database_table_name: 'supplier_goods_orders_events',
)]
abstract class Core_Supplier_GoodsOrder_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.SupplierGoodsOrder.';
	
	protected static string $event_base_class_name = Supplier_GoodsOrder_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;
	
	protected ?Supplier_GoodsOrder $_order = null;
	
	
	public function setOrder( Supplier_GoodsOrder $order ) : static
	{
		$this->order_id = $order->getId();
		$this->_order = $order;
		
		return $this;
	}
	
	public function getOrderId() : int
	{
		return $this->order_id;
	}
	
	public function getOrder() : Supplier_GoodsOrder
	{
		if($this->_order===null) {
			$this->_order = Supplier_GoodsOrder::get($this->order_id);
		}
		
		return $this->_order;
	}
	
	
	/**
	 * @param int $entity_id
	 *
	 * @return static[]
	 */
	public static function getEventsList( int $entity_id ) : array
	{
		return static::fetch(
			[''=>[
				'order_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
