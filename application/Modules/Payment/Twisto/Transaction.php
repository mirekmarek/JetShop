<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Payment\Twisto;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use JetApplication\Order;

#[DataModel_Definition(
	name: 'twisto_transaction',
	database_table_name: 'twisto_transaction',
	id_controller_class: DataModel_IDController_Passive::class
)]
class Transaction extends DataModel {
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $eshop_key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $created_date_time = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $twisto_transaction_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $twisto_invoice_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65535
	)]
	protected string $error_message = '';
	
	
	
	public static function newTransaction( Order $order, string $transaction_id ) : static
	{
		$eshop = $order->getEshop();
		$order_id = $order->getNumber();
		
		$exists = self::load([
			'eshop_key' => $eshop->getKey(),
			'AND',
			'order_id' => $order_id,
		]);
		
		if($exists) {
			return $exists;
		}
		
		$item = new static();
		
		$item->eshop_key = $eshop->getKey();
		$item->created_date_time = Data_DateTime::now();
		
		$item->order_id = $order_id;
		$item->twisto_transaction_id = $transaction_id;
		
		$item->save();
		
		return $item;
	}
	
	public function getOrderId() : int
	{
		return $this->order_id;
	}
	
	public function getTwistotransactionId(): string
	{
		return $this->twisto_transaction_id;
	}
	
	public function setTwistotransactionId( string $twisto_transaction_id ): void
	{
		$this->twisto_transaction_id = $twisto_transaction_id;
	}
	
	public function getTwistoInvoiceId(): string
	{
		return $this->twisto_invoice_id;
	}
	
	public function setTwistoInvoiceId( string $twisto_invoice_id ): void
	{
		$this->twisto_invoice_id = $twisto_invoice_id;
	}
	
	public function getErrorMessage(): string
	{
		return $this->error_message;
	}
	
	public function setErrorMessage( string $error_message ): void
	{
		$this->error_message = $error_message;
	}
	
	
	public static function get( int $order_id ) : ?static
	{
		return static::load([
			'order_id' => $order_id,
		]);
	}
}