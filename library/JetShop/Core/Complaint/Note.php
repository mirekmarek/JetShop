<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Complaint;

#[DataModel_Definition(
	name: 'complaints_notes',
	database_table_name: 'complaints_notes',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Complaint::class
)]
abstract class Core_Complaint_Note extends Entity_WithShopRelation {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $complaint_id = 0;
	
	protected ?Complaint $complaint = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_added = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $sent_to_customer = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $customer_email_address = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999
	)]
	protected string $note = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999
	)]
	protected string $files = '';
	
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
	
	
	
	public function getComplaintId(): int
	{
		return $this->complaint_id;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
		$this->complaint = $complaint;
		$this->complaint_id = $complaint->getId();
		$this->setShop( $complaint->getShop() );
	}
	
	
	public function getDateAdded(): ?Data_DateTime
	{
		return $this->date_added;
	}
	
	public function setDateAdded( Data_DateTime|string|null $date_added ): void
	{
		$this->date_added = Data_DateTime::catchDateTime( $date_added );
	}
	
	public function getSentToCustomer(): bool
	{
		return $this->sent_to_customer;
	}
	
	public function setSentToCustomer( bool $sent_to_customer ): void
	{
		$this->sent_to_customer = $sent_to_customer;
	}
	
	public function getCustomerEmailAddress(): string
	{
		return $this->customer_email_address;
	}
	
	public function setCustomerEmailAddress( string $customer_email_address ): void
	{
		$this->customer_email_address = $customer_email_address;
	}
	
	
	
	public function getNote(): string
	{
		return $this->note;
	}
	
	public function setNote( string $note ): void
	{
		$this->note = $note;
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
	
	public function getFiles(): array
	{
		if(!$this->files) {
			return [];
		}
		
		return explode(',', $this->files);
	}
	
	public function setFiles( array $files ): void
	{
		$this->files = implode(',', $files);
	}
	
	
	/**
	 * @param int $complaint_id
	 *
	 * @return static[]
	 */
	public static function getForComplaint( int $complaint_id ) : array
	{
		return static::fetch(
			['complaints_notes'=>[
				'complaint_id' => $complaint_id
			]],
			order_by: ['-date_added']
		);
	}
	
}