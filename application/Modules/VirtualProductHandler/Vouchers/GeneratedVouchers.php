<?php
/**
 * 
 */

namespace JetApplicationModule\VirtualProductHandler\Vouchers;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Data_DateTime;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Order_Item_SetItem;


#[DataModel_Definition(
	name: 'generated_vouchers',
	database_table_name: 'generated_vouchers',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	]
)]
class GeneratedVouchers extends DataModel
{

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $eshop_key = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $order_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $order_item_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $n = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $coupon_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $coupon_value = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $generated_date_time = null;
	

	public function getId() : int
	{
		return $this->id;
	}

	public function setEshopKey( string $value ) : void
	{
		$this->eshop_key = $value;
	}

	public function getEshopKey() : string
	{
		return $this->eshop_key;
	}

	public function setOrderId( int $value ) : void
	{
		$this->order_id = $value;
	}

	public function getOrderId() : int
	{
		return $this->order_id;
	}

	public function setOrderItemId( int $value ) : void
	{
		$this->order_item_id = $value;
	}

	public function getOrderItemId() : int
	{
		return $this->order_item_id;
	}
	
	public function getN(): int
	{
		return $this->n;
	}
	
	public function setN( int $n ): void
	{
		$this->n = $n;
	}
	
	
	
	public function setCouponCode( string $value ) : void
	{
		$this->coupon_code = $value;
	}
	
	public function getCouponCode() : string
	{
		return $this->coupon_code;
	}

	public function setCouponValue( float $value ) : void
	{
		$this->coupon_value = $value;
	}

	public function getCouponValue() : float
	{
		return $this->coupon_value;
	}

	public function setGeneratedDateTime( Data_DateTime|string|null $value ) : void
	{
		$this->generated_date_time = Data_DateTime::catchDateTime( $value );
	}

	public function getGeneratedDateTime() : Data_DateTime|null
	{
		return $this->generated_date_time;
	}
	
	public static function generated( Order $order, Order_Item|Order_Item_SetItem $item, int $n ) : ?static
	{
		$id = static::dataFetchOne(
			select: ['id'],
			where: [
				'eshop_key' => $order->getEshop()->getKey(),
				'AND',
				'order_id' => $order->getId(),
				'AND',
				'order_item_id' => $item->getId(),
				'AND',
				'n' => $n
			]
		);
		if(!$id) {
			return null;
		}
		
		return static::load( $id );
	}
}
