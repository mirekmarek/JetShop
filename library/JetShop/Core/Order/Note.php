<?php
namespace JetShop;

use Jet\Auth;
use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

use Jet\IO_Dir;
use Jet\SysConf_Path;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Order;
use JetApplication\Order_Note_File;

#[DataModel_Definition(
	name: 'orders_notes',
	database_table_name: 'orders_notes',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Order::class
)]
abstract class Core_Order_Note extends Entity_WithShopRelation {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $order_id = 0;
	
	protected ?Order $order = null;
	
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
		max_len: 255
	)]
	protected string $subject = '';
	
	
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
	
	
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function setOrder( Order $order ): void
	{
		$this->order = $order;
		$this->order_id = $order->getId();
		$this->setShop( $order->getShop() );
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
	
	public function getSubject(): string
	{
		return $this->subject;
	}
	
	public function setSubject( string $subject ): void
	{
		$this->subject = $subject;
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
	
	protected function getFilesDirPath() : string
	{
		$dir = SysConf_Path::getData().'order_note_files/'.Auth::getCurrentUser()->getId().'/'.$this->getShop()->getKey().'/'.$this->order_id.'/';
		
		if(!IO_Dir::exists($dir)) {
			IO_Dir::create( $dir );
		}
		
		return $dir;
	}
	
	/**
	 * @return Order_Note_File[]
	 */
	public function getFiles() : array
	{
		if(!$this->files) {
			return [];
		}
		
		$_files =  explode(',', $this->files);
		
		$files = [];
		
		foreach($_files as $file_name) {
			$files[] = new Order_Note_File( $this->getFilesDirPath(), $file_name );
		}
		
		return $files;
	}
	
	
	public function setFiles( array $files ): void
	{
		$this->files = implode(',', $files);
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
			order_by: ['-date_added']
		);
	}
	
}