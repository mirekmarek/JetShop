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
use JetApplication\Complaint;
use JetApplication\Complaint_event;

/**
 *
 */
#[DataModel_Definition(
	name: 'complaint_event',
	database_table_name: 'complaints_events',
)]
class Core_Complaint_Event extends EShopEntity_Event
{

	protected static string $handler_module_name_prefix = 'Events.Complaint.';
	
	protected static string $event_base_class_name = Complaint_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $complaint_id = 0;

	protected ?Complaint $_complaint = null;
	
	
	public function setComplaint( Complaint $complaint ) : static
	{
		$this->_complaint = $complaint;
		$this->complaint_id = $complaint->getId();

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
	
	/**
	 * @param int $entity_id
	 *
	 * @return static[]
	 */
	public static function getEventsList( int $entity_id ) : array
	{
		return static::fetch(
			[''=>[
				'complaint_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
