<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;

use JetApplication\Entity_Event;
use JetApplication\Complaint_Event_HandlerModule;
use JetApplication\Complaint;
use JetApplication\Complaint_event;

/**
 *
 */
#[DataModel_Definition(
	name: 'complaint_event',
	database_table_name: 'complaints_events',
)]
class Core_Complaint_Event extends Entity_Event
{

	protected static string $handler_module_name_prefix = 'Events.Complaint.';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $complaint_id = 0;

	protected ?Complaint $_complaint = null;
	
	
	public static function getEventHandlerModule( string $event_name ) : Complaint_Event_HandlerModule
	{
		/**
		 * @var Complaint_Event $this
		 * @var Complaint_Event_HandlerModule $module
		 */
		$module = Application_Modules::moduleInstance( static::getHandlerModuleNamePrefix().$event_name );
		
		return $module;
	}

	public function setComplaintId( int $value ) : static
	{
		$this->complaint_id = $value;

		return $this;
	}

	public function getComplaintId() : int
	{
		return $this->complaint_id;
	}

	public function getComplaint() : Complaint
	{
		if($this->_complaint===null) {
			$this->_complaint = Complaint::get($this->complaint_id);
		}

		return $this->_complaint;
	}
	
	public function getHandlerModule() : Complaint_Event_HandlerModule
	{
		/**
		 * @var Complaint_event $this
		 * @var Complaint_Event_HandlerModule $module
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

	public static function newEvent( Complaint $complaint, string $event ) : Complaint_Event
	{
		$e = new Complaint_Event();
		$e->setEvent( $event );
		$e->setShop( $complaint->getShop() );
		$e->setComplaintId( $complaint->getId() );
		$e->created_date_time = Data_DateTime::now();

		return $e;
	}
	
	
	/**
	 * @param int $complaint_id
	 *
	 * @return static[]
	 */
	public static function getForComplaint( int $complaint_id ) : array
	{
		return static::fetch(
			[''=>[
				'complaint_id' => $complaint_id
			]],
			order_by: ['-id']
		);
	}
	
}
