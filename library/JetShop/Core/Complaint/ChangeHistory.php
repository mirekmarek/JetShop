<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Entity_WithShopRelation;
use JetApplication\Complaint;
use JetApplication\Complaint_ChangeHistory_Item;

#[DataModel_Definition(
	name: 'complaints_change_history',
	database_table_name: 'complaints_change_history'
)]
abstract class Core_Complaint_ChangeHistory extends Entity_WithShopRelation {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $complaint_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_added = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999
	)]
	protected string $comment = '';
	
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
	
	/**
	 * @var Complaint_ChangeHistory_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Complaint_ChangeHistory_Item::class
	)]
	protected array $items = [];
	
	
	public function getArrayKeyValue(): string
	{
		return $this->id;
	}
	
	public function getId(): int
	{
		return $this->id;
	}
	
	public function setId( int $id ): void
	{
		$this->id = $id;
	}
	
	public function getComplaintId(): int
	{
		return $this->complaint_id;
	}
	
	public function setComplaint( Complaint $complaint ): void
	{
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
	
	public function getComment(): string
	{
		return $this->comment;
	}
	
	public function setComment( string $comment ): void
	{
		$this->comment = $comment;
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
	
	
	/**
	 * @param int $complaint_id
	 *
	 * @return static[]
	 */
	public static function getForComplaint( int $complaint_id ) : array
	{
		return static::fetch(
			['complaints_change_history'=>[
				'complaint_id' => $complaint_id
			]],
			order_by: ['-date_added']
		);
	}
	
	public function addChange(
		string $property,
		string $old_value,
		string $new_value
	) : void
	{
		$item = new Complaint_ChangeHistory_Item();
		$item->setProperty( $property );
		$item->setOldValue( $old_value );
		$item->setNewValue( $new_value );
		$item->setComplaintId( $this->getId() );
		
		$this->items[] = $item;
	}
	
	public function hasChange() : bool
	{
		return count($this->items)>0;
	}
	
	/**
	 * @return Complaint_ChangeHistory_Item[]
	 */
	public function getChanges(): array
	{
		return $this->items;
	}
	
	
}