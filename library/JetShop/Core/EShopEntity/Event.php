<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Modules;
use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_PropertyFilter;
use Jet\Data_DateTime;

use JetApplication\EShop;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Trait;
use JetApplication\Event_HandlerModule;
use ReflectionClass;

/**
 *
 */
#[DataModel_Definition]
abstract class Core_EShopEntity_Event extends EShopEntity_Basic implements EShopEntity_HasEShopRelation_Interface
{
	use EShopEntity_HasEShopRelation_Trait;
	protected static string $handler_module_name_prefix = '';
	
	protected static string $event_base_class_name = '';
	
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
	protected string $context_object_class = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $context_object_id = 0;
	
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
	protected bool $do_not_handle_externals = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $externals_handled = false;
	
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
	protected bool $internals_handled = false;
	
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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $administrator = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $administrator_id = 0;
	
	public static function new() : static
	{
		return new static();
	}
	
	public static function getHandlerModuleNamePrefix(): string
	{
		return static::$handler_module_name_prefix;
	}
	
	public static function setHandlerModuleNamePrefix( string $handler_module_name_prefix ): void
	{
		static::$handler_module_name_prefix = $handler_module_name_prefix;
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
	
	public function setEvent( string $value ) : static
	{
		$this->event = $value;
		
		return $this;
	}
	
	public function getEvent() : string
	{
		return $this->event;
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
	
	public function getDateAdded() : Data_DateTime
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
	
	public function setDoNotHandleExternals( bool $value ) : static
	{
		$this->do_not_handle_externals = $value;
		
		return $this;
	}
	
	public function getDoNotHandleExternals() : bool
	{
		return $this->do_not_handle_externals;
	}
	
	public function setExternalsHandled( bool $value ) : static
	{
		$this->externals_handled = $value;
		
		return $this;
	}
	
	public function getExternalsHandled() : bool
	{
		return $this->externals_handled;
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
	
	public function setInternalsHandled( bool $value ) : static
	{
		$this->internals_handled = $value;
		
		return $this;
	}
	
	public function getInternalsHandled() : bool
	{
		return $this->internals_handled;
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
		$this->save();
	}
	
	public function getErrorMessage() : string
	{
		return $this->error_message;
	}
	
	public function getAdministrator(): string
	{
		return $this->administrator;
	}
	
	public function setAdministrator( string $administrator ): void
	{
		$this->administrator = $administrator;
	}
	
	public function getAdministratorId(): int
	{
		return $this->administrator_id;
	}
	
	public function setAdministratorId( int $administrator_id ): void
	{
		$this->administrator_id = $administrator_id;
	}
	
	public function getContextObjectClass(): string
	{
		return $this->context_object_class;
	}
	
	public function setContextObjectClass( string $context_object_class ): void
	{
		$this->context_object_class = $context_object_class;
	}
	
	public function getContextObjectId(): int
	{
		return $this->context_object_id;
	}
	
	public function setContextObjectId( int $context_object_id ): void
	{
		$this->context_object_id = $context_object_id;
	}
	
	public function setContext( EShopEntity_Basic $context ) : void
	{
		$this->context_object_class = get_class($context);
		$this->context_object_id = $context->getId();
	}
	
	public function getContext() : ?EShopEntity_Basic
	{
		if(
			!$this->context_object_class ||
			!$this->context_object_id
		) {
			return null;
		}
		
		/**
		 * @var EShopEntity_Basic $class
		 */
		$class = $this->context_object_class;
		
		return $class::load( $this->context_object_id );
	}
	
	
	public function getHandlerModuleName() : string
	{
		return static::getHandlerModuleNamePrefix().$this->event;
	}
	
	
	public function getHandlerModule() : ?Event_HandlerModule
	{
		
		if(!Application_Modules::moduleIsActivated( $this->getHandlerModuleName() )) {
			return null;
		}
		
		$module = Application_Modules::moduleInstance( $this->getHandlerModuleName() );
		/**
		 * @var Event_HandlerModule $module
		 * @var EShopEntity_Event $this
		 */
		$module->init( $this );
		
		return $module;
	}
	
	public function handle() : bool
	{
		$this->save();
		
		return $this->getHandlerModule()?->handle()??false;
	}
	
	public function handleImmediately() : bool
	{
		$this->handled_immediately = true;
		$this->save();
		
		return $this->handle();
	}
	
	public static function getCode() : string
	{
		return str_replace( static::$event_base_class_name.'_', '', static::class );
	}
	
	public function init( EShop $eshop ) : void
	{
		$this->setEshop( $eshop );
		$this->event = static::getCode();
		$this->created_date_time = Data_DateTime::now();
		
		$admin = Auth::getCurrentUser();
		if($admin) {
			$this->setAdministrator( $admin->getName() );
			$this->setAdministratorId( $admin->getId() );
		}
	}
	
	public static function initByData( array $this_data, array $related_data = [], DataModel_PropertyFilter $load_filter = null) : static
	{
		$ref = new ReflectionClass(static::class);
		if(!$ref->isAbstract()) {
			return parent::initByData( $this_data, $related_data, $load_filter );
		}
		
		$class = static::$event_base_class_name.'_'.$this_data['event'];
		/**
		 * @var EShopEntity_Event $class
		 */
		
		return $class::initByData( $this_data, $related_data, $load_filter );
		
	}
	
	/**
	 * @param int $entity_id
	 *
	 * @return static[]
	 */
	abstract public static function getEventsList( int $entity_id ) : array;
}
