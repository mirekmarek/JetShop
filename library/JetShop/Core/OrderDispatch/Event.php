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
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'order_dispatch_event',
	database_table_name: 'orders_dispatch_events',
)]
class Core_OrderDispatch_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.OrderDispatch.';
	
	protected static string $event_base_class_name = OrderDispatch_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_dispatch_id = 0;
	
	protected ?OrderDispatch $_order_dispatch = null;
	
	public function setOrderDispatch( OrderDispatch $value ) : static
	{
		$this->_order_dispatch = $value;
		$this->order_dispatch_id = $value->getId();
		
		return $this;
	}
	
	public function getOrderDispatchId() : int
	{
		return $this->order_dispatch_id;
	}
	
	public function getOrderDispatch() : OrderDispatch
	{
		if($this->_order_dispatch===null) {
			$this->_order_dispatch = OrderDispatch::load($this->order_dispatch_id);
		}
		
		return $this->_order_dispatch;
	}
	
	public static function getEventsList( int $entity_id ): array
	{
		return static::fetch(
			[''=>[
				'order_dispatch_id' => $entity_id
			]],
			order_by: ['-id']
		);

	}
}
