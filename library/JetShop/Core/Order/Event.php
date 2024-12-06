<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;

use JetApplication\Entity_Event;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\Order;
use JetApplication\Order_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'order_event',
	database_table_name: 'orders_events',
)]
class Core_Order_Event extends Entity_Event
{

	protected static string $handler_module_name_prefix = 'Events.Order.';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;

	protected ?Order $_order = null;
	
	public static function getEventHandlerModule( string $event_name ) : Order_Event_HandlerModule
	{
		/**
		 * @var Order_Event $this
		 * @var Order_Event_HandlerModule $module
		 */
		$module = Application_Modules::moduleInstance( static::getHandlerModuleNamePrefix().$event_name );
		
		return $module;
	}

	public function setOrderId( int $value ) : static
	{
		$this->order_id = $value;

		return $this;
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

	public function getHandlerModule() : ?Order_Event_HandlerModule
	{
		/**
		 * @var Order_Event $this
		 * @var Order_Event_HandlerModule $module
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
	
	public static function newEvent( Order $order, string $event ) : Order_Event
	{
		$e = new Order_Event();
		$e->setEvent( $event );
		$e->setEshop( $order->getEshop() );
		$e->setOrderId( $order->getId() );
		$e->created_date_time = Data_DateTime::now();
		
		$admin = Auth::getCurrentUser();
		if($admin) {
			$e->setAdministrator( $admin->getName() );
			$e->setAdministratorId( $admin->getId() );
		}
		

		return $e;
	}
	
	/**
	 * @param int $order_id
	 *
	 * @return static[]
	 */
	public static function getForOrder( int $order_id ) : array
	{
		return static::fetch(
			[''=>[
				'order_id' => $order_id
			]],
			order_by: ['-id']
		);
	}
	
}
