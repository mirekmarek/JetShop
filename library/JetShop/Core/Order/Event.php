<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_DateTime;

use JetApplication\CommonEntity_ShopRelationTrait;
use JetApplication\Order_Event_HandlerModule;
use JetApplication\Order;
use JetApplication\Order_event;

/**
 *
 */
#[DataModel_Definition(
	name: 'order_event',
	database_table_name: 'orders_events',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class Core_Order_Event extends DataModel
{
	use CommonEntity_ShopRelationTrait;

	protected static string $handler_module_name_prefix = 'Order.Events.';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_id = 0;

	protected ?Order $_order = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $event = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $context_1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
	)]
	protected string $context_2 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $context_3 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999,
	)]
	protected string $internal_note = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999,
	)]
	protected string $note_for_customer = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $created_date_time = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $do_not_set_external_status = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $external_status_set = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $do_not_send_notification = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $notification_sent = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $status_set = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $handled_immediately = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $handled = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $handled_date_time = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999,
	)]
	protected string $error_message = '';


	public static function getHandlerModuleNamePrefix(): string
	{
		return static::$handler_module_name_prefix;
	}

	public static function setHandlerModuleNamePrefix( string $handler_module_name_prefix ): void
	{
		static::$handler_module_name_prefix = $handler_module_name_prefix;
	}


	/**
	 * @param int $id
	 * @return static|null
	 */
	public static function get( int $id ) : static|null
	{
		return static::load( $id );
	}

	/**
	 * @return iterable
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		$list = static::fetchInstances( $where );
		
		return $list;
	}

	public function getId() : int
	{
		return $this->id;
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


	public function setEvent( string $value ) : static
	{
		$this->event = $value;

		return $this;
	}

	public function getEvent() : string
	{
		return $this->event;
	}

	public function setContext1( string $value ) : static
	{
		$this->context_1 = $value;

		return $this;
	}

	public function getContext1() : string
	{
		return $this->context_1;
	}

	public function setContext2( string $value ) : static
	{
		$this->context_2 = $value;

		return $this;
	}

	public function getContext2() : string
	{
		return $this->context_2;
	}

	public function setContext3( string $value ) : static
	{
		$this->context_3 = $value;

		return $this;
	}

	public function getContext3() : string
	{
		return $this->context_3;
	}

	public function setInternalNote( string $value ) : static
	{
		$this->internal_note = $value;

		return $this;
	}

	public function getInternalNote() : string
	{
		return $this->internal_note;
	}

	public function setNoteForCustomer( string $value ) : static
	{
		$this->note_for_customer = $value;

		return $this;
	}

	public function getNoteForCustomer() : string
	{
		return $this->note_for_customer;
	}

	public function getCreatedDateTime() : Data_DateTime
	{
		return $this->created_date_time;
	}

	public function setHandled( bool $value ) : void
	{
		$this->handled = $value;
		if($value) {
			$this->handled_date_time = Data_DateTime::now();
		} else {
			$this->handled_date_time = null;
		}
	}

	public function getHandled() : bool
	{
		return $this->handled;
	}

	public function setDoNotSetExternalStatus( bool $value ) : static
	{
		$this->do_not_set_external_status = $value;

		return $this;
	}

	public function getDoNotSetExternalStatus() : bool
	{
		return $this->do_not_set_external_status;
	}

	public function setExternalStatusSet( bool $value ) : static
	{
		$this->external_status_set = $value;

		return $this;
	}

	public function getExternalStatusSet() : bool
	{
		return $this->external_status_set;
	}

	public function setDoNotSendNotification( bool $value ) : static
	{
		$this->do_not_send_notification = $value;

		return $this;
	}

	public function getDoNotSendNotification() : bool
	{
		return $this->do_not_send_notification;
	}

	public function setNotificationSent( bool $value ) : static
	{
		$this->notification_sent = $value;

		return $this;
	}

	public function getNotificationSent() : bool
	{
		return $this->notification_sent;
	}

	public function setStatusSet( bool $value ) : static
	{
		$this->status_set = $value;

		return $this;
	}

	public function getStatusSet() : bool
	{
		return $this->status_set;
	}

	public function getHandledImmediately() : bool
	{
		return $this->handled_immediately;
	}

	public function getHandledDateTime() : Data_DateTime|null
	{
		return $this->handled_date_time;
	}

	public function setErrorMessage( string $value ) : void
	{
		$this->error_message = $value;
	}

	public function getErrorMessage() : string
	{
		return $this->error_message;
	}

	public function getHandlerModuleName() : string
	{
		return static::getHandlerModuleNamePrefix().$this->event;
	}

	public function getHandlerModule() : Order_Event_HandlerModule
	{
		/**
		 * @var Order_event $this
		 * @var Order_Event_HandlerModule $module
		 */
		$module = Application_Modules::moduleInstance( $this->getHandlerModuleName() );
		$module->init( $this );

		return $module;
	}

	public function handle() : bool
	{
		return $this->getHandlerModule()->handle();
	}

	public function handleImmediately() : bool
	{
		$this->handled_immediately = true;

		return $this->handle();
	}

	public static function newEvent( int $order_id, string $event ) : Order_Event
	{
		$e = new Order_Event();
		$e->setEvent( $event );
		$e->setOrderId( $order_id );
		$e->created_date_time = Data_DateTime::now();

		return $e;
	}
}
