<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Payment\GoPay;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;

#[DataModel_Definition(
	name: 'gopay_payments',
	database_table_name: 'gopay_payments',
	id_controller_class: DataModel_IDController_Passive::class
)]
class PaymentPair extends DataModel {

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		is_key: true
	)]
	protected int $order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_id: true,
		is_key: true
	)]
	protected string $payment_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $payment_status = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_time = null;
	
	/**
	 * @param int $order_id
	 * @return static[]
	 */
	public static function getPayments( int $order_id ) : array
	{
		return static::fetch([''=>['order_id'=>$order_id]], item_key_generator: function( PaymentPair $payment_pair ) : string {
			return $payment_pair->getPaymentId();
		});
	}
	
	public static function setPaymentId( int $order_id, string $payment_id ) : void
	{
		$rec = new static();
		$rec->order_id = $order_id;
		$rec->payment_id = $payment_id;
		$rec->date_time = Data_DateTime::now();
		$rec->payment_status = '';
		$rec->save();
	}
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function getPaymentId(): string
	{
		return $this->payment_id;
	}
	
	public function getPaymentStatus(): string
	{
		return $this->payment_status;
	}
	
	public function getDateTime(): ?Data_DateTime
	{
		return $this->date_time;
	}
	
	public function setPaymentStatus( string $payment_status ) : void
	{
		$this->payment_status = $payment_status;
		$this->save();
	}
	
	
}