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
use JetApplication\Order;
use JetApplication\Order_Event;

#[DataModel_Definition(
	name: 'order_event',
	database_table_name: 'orders_events',
)]
abstract class Core_Order_Event extends EShopEntity_Event
{

	protected static string $handler_module_name_prefix = 'Events.Order.';
	
	protected static string $event_base_class_name = Order_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;

	protected ?Order $_order = null;
	

	public function setOrder( Order $order ) : static
	{
		$this->order_id = $order->getId();
		$this->_order = $order;

		return $this;
	}
	
	public function setOrderId( int $order_id ) : void
	{
		$this->order_id = $order_id;
	}

	public function getOrderId() : int
	{
		return $this->order_id;
	}

	public function getOrder() : Order
	{
		if($this->_order===null) {
			$this->_order = Order::get($this->order_id);
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
