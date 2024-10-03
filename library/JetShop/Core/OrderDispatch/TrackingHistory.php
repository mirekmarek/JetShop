<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel;
use JetApplication\OrderDispatch_TrackingHistory;


#[DataModel_Definition(
	name: 'order_dispatch_tracking',
	database_table_name: 'order_dispatches_tracking',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_OrderDispatch_TrackingHistory extends DataModel
{
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_key: true,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $dispatch_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50
	)]
	protected string $checksum = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100
	)]
	protected string $carrier_status_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255
	)]
	protected string $carrier_status_description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true
	)]
	protected ?Data_DateTime $date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255
	)]
	protected string $carrier_notes = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $delivered_person = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50
	)]
	protected string $our_status = '';
	
	public function __construct(
		int $dispatch_id,
		string $carrier_status_id,
		string $carrier_status_description,
		Data_DateTime $date_time,
		string $carrier_notes,
		string $delivered_person,
		string $our_status
	) {
		$this->dispatch_id = $dispatch_id;
		$this->carrier_status_id = $carrier_status_id;
		$this->carrier_status_description = $carrier_status_description;
		$this->date_time = $date_time;
		$this->carrier_notes = $carrier_notes;
		$this->delivered_person = $delivered_person;
		$this->our_status = $our_status;
	}
	
	protected function generateChecksum() : void
	{
		$this->checksum = md5(
			$this->dispatch_id
			.$this->carrier_status_id
			.$this->carrier_status_description
			.$this->date_time->toString()
			.$this->carrier_notes
			.$this->delivered_person
			.$this->our_status
		);
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function getDispatchId(): int
	{
		return $this->dispatch_id;
	}
	
	public function getChecksum(): string
	{
		return $this->checksum;
	}
	
	public function getCarrierStatusId(): string
	{
		return $this->carrier_status_id;
	}

	public function getCarrierStatusDescription(): string
	{
		return $this->carrier_status_description;
	}
	
	public function getDateTime(): Data_DateTime
	{
		return $this->date_time;
	}

	public function getCarrierNotes(): string
	{
		return $this->carrier_notes;
	}

	public function getDeliveredPerson(): string
	{
		return $this->delivered_person;
	}
	
	public function getOurStatus(): string
	{
		return $this->our_status;
	}
	
	/**
	 * @param int $dispatch_id
	 * @return static[]
	 */
	public static function getForOrderDispatch( int $dispatch_id ) : array
	{
		return static::fetch(
			[''=>[
				'dispatch_id' => $dispatch_id
			]],
			order_by: ['date_time'],
			item_key_generator: function( OrderDispatch_TrackingHistory $item ) : string
			{
				return $item->getChecksum();
			}
		);
	}
	
	
}