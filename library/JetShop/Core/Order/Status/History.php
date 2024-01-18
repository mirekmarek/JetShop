<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\Order;

#[DataModel_Definition(
	name: 'orders_status_history',
	database_table_name: 'orders_status_history',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Order::class
)]
abstract class Core_Order_Status_History extends DataModel_Related_1toN {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $order_id = 0;
	
	protected ?Order $__order = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $status_is = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
		is_key: true,
	)]
	protected ?Data_DateTime $date_added = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $customer_notified = false;
	
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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	protected bool $comment_is_visible_for_customer = false;
	
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
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}
	
	public function getStatusIs(): int
	{
		return $this->status_is;
	}
	
	public function setStatusIs( int $status_is ): void
	{
		$this->status_is = $status_is;
	}
	
	public function getDateAdded(): ?Data_DateTime
	{
		return $this->date_added;
	}
	
	public function setDateAdded( Data_DateTime|string|null $date_added ): void
	{
		$this->date_added = Data_DateTime::catchDateTime( $date_added );
	}
	
	public function isCustomerNotified(): bool
	{
		return $this->customer_notified;
	}
	
	public function setCustomerNotified( bool $customer_notified ): void
	{
		$this->customer_notified = $customer_notified;
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
	
	public function isCommentIsVisibleForCustomer(): bool
	{
		return $this->comment_is_visible_for_customer;
	}
	
	public function setCommentIsVisibleForCustomer( bool $comment_is_visible_for_customer ): void
	{
		$this->comment_is_visible_for_customer = $comment_is_visible_for_customer;
	}
	
	
	
}