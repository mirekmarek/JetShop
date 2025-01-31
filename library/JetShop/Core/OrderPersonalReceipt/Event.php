<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;

use JetApplication\EShopEntity_Event;
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;
use JetApplication\OrderPersonalReceipt_Event_HandlerModule;

/**
 *
 */
#[DataModel_Definition(
	name: 'order_personal_receipt_event',
	database_table_name: 'order_personal_receipts_events',
)]
class Core_OrderPersonalReceipt_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.OrderPersonalReceipt.';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_personal_receipt_id = 0;
	
	protected ?OrderPersonalReceipt $_order_dispatch = null;
	
	
	public static function getEventHandlerModule( string $event_name ) : OrderPersonalReceipt_Event_HandlerModule
	{
		/**
		 * @var OrderPersonalReceipt_Event $this
		 * @var OrderPersonalReceipt_Event_HandlerModule $module
		 */
		$module = Application_Modules::moduleInstance( static::getHandlerModuleNamePrefix().$event_name );
		
		return $module;
	}
	
	public function setOrderPersonalReceiptId( int $value ) : static
	{
		$this->order_personal_receipt_id = $value;
		
		return $this;
	}
	
	public function getOrderPersonalReceiptId() : int
	{
		return $this->order_personal_receipt_id;
	}
	
	public function getOrderPersonalReceipt() : OrderPersonalReceipt
	{
		if($this->_order_dispatch===null) {
			$this->_order_dispatch = OrderPersonalReceipt::load($this->order_personal_receipt_id);
		}
		
		return $this->_order_dispatch;
	}
	
	public function getHandlerModule() : ?OrderPersonalReceipt_Event_HandlerModule
	{
		/**
		 * @var OrderPersonalReceipt_Event $this
		 * @var OrderPersonalReceipt_Event_HandlerModule $module
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
	
	public static function newEvent( OrderPersonalReceipt $order_dispatch, string $event ) : OrderPersonalReceipt_Event
	{
		$e = new OrderPersonalReceipt_Event();
		$e->setEvent( $event );
		$e->setEshop( $order_dispatch->getEshop() );
		$e->setOrderPersonalReceiptId( $order_dispatch->getId() );
		$e->created_date_time = Data_DateTime::now();
		
		return $e;
	}
}
