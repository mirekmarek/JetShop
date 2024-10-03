<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Payment\GoPay;

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
	
	public static function getPaymentId( int $order_id ) : ?string
	{
		$payment_id = static::dataFetchOne(select: ['payment_id'], where: ['order_id'=>$order_id]);
		return $payment_id?:null;
	}
	
	public static function setPaymentId( int $order_id, string $payment_id ) : void
	{
		$exists = static::getPaymentId($order_id);
		if($exists) {
			static::updateData(
				data:['payment_id'=>$payment_id],
				where:['order_id'=>$order_id]
			);
			return;
		}
		
		$rec = new static();
		$rec->order_id = $order_id;
		$rec->payment_id = $payment_id;
		$rec->save();
		
	}
}