<?php
/**
 *
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;

use JetApplication\EShopEntity_Event;
use JetApplication\OrderDispatch;
use JetApplication\OrderDispatch_Event;
use JetApplication\OrderDispatch_Event_HandlerModule;

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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_dispatch_id = 0;
	
	protected ?OrderDispatch $_order_dispatch = null;
	
	
	public static function getEventHandlerModule( string $event_name ) : OrderDispatch_Event_HandlerModule
	{
		/**
		 * @var OrderDispatch_Event $this
		 * @var OrderDispatch_Event_HandlerModule $module
		 */
		$module = Application_Modules::moduleInstance( static::getHandlerModuleNamePrefix().$event_name );
		
		return $module;
	}
	
	public function setOrderDispatchId( int $value ) : static
	{
		$this->order_dispatch_id = $value;
		
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
	
	public function getHandlerModule() : ?OrderDispatch_Event_HandlerModule
	{
		/**
		 * @var OrderDispatch_Event $this
		 * @var OrderDispatch_Event_HandlerModule $module
		 */
		if(!Application_Modules::moduleIsActivated( $this->getHandlerModuleName() )) {
			return null;
		}
		
		$module = Application_Modules::moduleInstance( $this->getHandlerModuleName() );
		$module->init( $this );
		
		return $module;
	}
	
	public function handle() : bool
	{
		return $this->getHandlerModule()->handle();
	}
	
	public static function newEvent( OrderDispatch $order_dispatch, string $event ) : OrderDispatch_Event
	{
		$e = new OrderDispatch_Event();
		$e->setEvent( $event );
		$e->setEshop( $order_dispatch->getEshop() );
		$e->setOrderDispatchId( $order_dispatch->getId() );
		$e->created_date_time = Data_DateTime::now();
		
		return $e;
	}
}
