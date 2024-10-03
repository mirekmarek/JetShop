<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\Entity_WithShopRelation;
use JetApplication\ReturnOfGoods;

#[DataModel_Definition(
	name: 'return_of_goods_notes',
	database_table_name: 'return_of_goods_notes',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: ReturnOfGoods::class
)]
abstract class Core_ReturnOfGoods_Note extends Entity_WithShopRelation {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
	)]
	protected int $return_of_goods_id = 0;
	
	protected ?ReturnOfGoods $return_of_goods = null;
	
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
	
	
	
	public function getReturnOfGoodsId(): int
	{
		return $this->return_of_goods_id;
	}
	
	public function setReturnOfGoods( ReturnOfGoods $return_of_goods ): void
	{
		$this->return_of_goods = $return_of_goods;
		$this->return_of_goods_id = $return_of_goods->getId();
		$this->setShop( $return_of_goods->getShop() );
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
	 * @param int $return_of_goods_id
	 *
	 * @return static[]
	 */
	public static function getForReturnOfGoods( int $return_of_goods_id ) : array
	{
		return static::fetch(
			['return_of_goods_notes'=>[
				'return_of_goods_id' => $return_of_goods_id
			]],
			order_by: ['-date_added']
		);
	}
	
}