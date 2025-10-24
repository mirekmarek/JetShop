<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EMailMarketing\EComail;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

#[DataModel_Definition(
	name: 'cart_to_be_send',
	database_table_name: 'ecomail_cart_to_be_send',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name' => 'id']
)]
class CartToBeSend extends DataModel {
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $cart_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $eshop_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $customer_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $inserted_date_time = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $items = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $items_count = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $processed = false;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $processed_date_time = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $process_result = '';
	
	
	
	public function getCartId(): string
	{
		return $this->cart_id;
	}
	
	public function setCartId( string $cart_id ): void
	{
		$this->cart_id = $cart_id;
	}
	
	public function getEshopKey(): string
	{
		return $this->eshop_key;
	}
	
	public function setEshopKey( string $eshop_key ): void
	{
		$this->eshop_key = $eshop_key;
	}
	
	public function getCustomerId(): int
	{
		return $this->customer_id;
	}
	
	public function setCustomerId( int $customer_id ): void
	{
		$this->customer_id = $customer_id;
	}
	
	public function getInsertedDateTime(): ?Data_DateTime
	{
		return $this->inserted_date_time;
	}
	
	public function setInsertedDateTime( ?Data_DateTime $inserted_date_time ): void
	{
		$this->inserted_date_time = $inserted_date_time;
	}
	
	public function getItems(): string
	{
		return $this->items;
	}
	
	public function setItems( string $items ): void
	{
		$this->items = $items;
	}
	
	public function getItemsCount(): int
	{
		return $this->items_count;
	}
	
	public function setItemsCount( int $items_count ): void
	{
		$this->items_count = $items_count;
	}
	
	public function processed( string $result ): void
	{
		$this->processed = true;
		$this->processed_date_time = Data_DateTime::now();
		$this->process_result = $result;
		$this->save();
	}
	
	public function getProcessed(): bool
	{
		return $this->processed;
	}
	
	public function setProcessed( bool $processed ): void
	{
		$this->processed = $processed;
	}
	
	public function getProcessedDateTime(): ?Data_DateTime
	{
		return $this->processed_date_time;
	}
	
	public function setProcessedDateTime( ?Data_DateTime $processed_date_time ): void
	{
		$this->processed_date_time = $processed_date_time;
	}
	
	public function getProcessResult(): string
	{
		return $this->process_result;
	}
	
	public function setProcessResult( string $process_result ): void
	{
		$this->process_result = $process_result;
	}


	
}