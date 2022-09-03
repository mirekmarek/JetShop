<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;

#[DataModel_Definition(
	name: 'order_item',
	database_table_name: 'orders_items',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Order::class
)]
abstract class Core_Order_Item extends DataModel_Related_1toN {

	const ITEM_TYPE_PRODUCT = 'product';
	const ITEM_TYPE_VIRTUAL_PRODUCT = 'virtual_product';
	const ITEM_TYPE_SERVICE = 'service';
	const ITEM_TYPE_GIFT = 'gift';
	const ITEM_TYPE_PAYMENT = 'payment';
	const ITEM_TYPE_DELIVERY = 'delivery';
	const ITEM_TYPE_DISCOUNT = 'discount';

	const DISCOUNT_TYPE_PRODUCTS_AMOUNT = 'disc_products_amount';
	const DISCOUNT_TYPE_PRODUCTS_PERCENT = 'disc_products_percent';

	const DISCOUNT_TYPE_SERVICE_AMOUNT = 'disc_service_amount';
	const DISCOUNT_TYPE_SERVICE_PERCENT = 'disc_service_percent';

	const DISCOUNT_TYPE_DELIVERY_AMOUNT = 'disc_delivery_amount';
	const DISCOUNT_TYPE_DELIVERY_PERCENT = 'disc_delivery_percent';

	const DISCOUNT_TYPE_PAYMENT_AMOUNT = 'disc_payment_amount';
	const DISCOUNT_TYPE_PAYMENT_PERCENT = 'disc_payment_percent';

	const DISCOUNT_TYPE_TOTAL_AMOUNT = 'disc_total_amount';
	const DISCOUNT_TYPE_TOTAL_PERCENT = 'disc_total_percent';

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
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $sub_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $sub_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
	)]
	protected bool $available = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $quantity = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $item_amount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $vat_rate = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $delivery_info = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $delivery_delay = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected ?Data_DateTime $delivery_date = null;

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 64,
	)]
	protected string $warehouse_code = '';

	public function getArrayKeyValue(): string
	{
		return $this->id;
	}

	public function getOrder() : Order
	{
		return $this->__order;
	}

	public function setOrder( Order $_order ) : void
	{
		$this->__order = $_order;
	}


	public function getType() : string
	{
		return $this->type;
	}

	public function isDiscount() : bool
	{
		return $this->type == Order_Item::ITEM_TYPE_DISCOUNT;
	}

	public function setType( string $type ) : void
	{
		$this->type = $type;
	}

	public function getSubType() : string
	{
		return $this->sub_type;
	}

	public function setSubType( string $sub_type ) : void
	{
		$this->sub_type = $sub_type;
	}

	public function getCode() : string
	{
		return $this->code;
	}

	public function setCode( string $code ) : void
	{
		$this->code = $code;
	}

	public function getSubCode() : string
	{
		return $this->sub_code;
	}

	public function setSubCode( string $sub_code ) : void
	{
		$this->sub_code = $sub_code;
	}

	public function getProductId() : string
	{
		return $this->product_id;
	}

	public function setProductId( string $product_id ) : void
	{
		$this->product_id = $product_id;
	}

	public function isAvailable() : bool
	{
		return $this->available;
	}

	public function setAvailable( bool $available ) : void
	{
		$this->available = $available;
	}

	public function getTitle() : string
	{
		return $this->title;
	}

	public function setTitle( string $title ) : void
	{
		$this->title = $title;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getQuantity() : int
	{
		return $this->quantity;
	}

	public function setQuantity( int $quantity ) : void
	{
		$this->quantity = $quantity;
		$this->total_amount = $this->item_amount*$this->quantity;
	}

	public function getItemAmount() : float
	{
		return $this->item_amount;
	}

	public function setItemAmount( float $item_amount ) : void
	{
		$this->item_amount = $item_amount;
		$this->total_amount = $item_amount*$this->quantity;
	}

	public function getVatRate() : float
	{
		return $this->vat_rate;
	}

	public function setVatRate( float $vat_rate ) : void
	{
		$this->vat_rate = $vat_rate;
	}

	public function setTotalAmount( float $total_amount ): void
	{
		$this->total_amount = $total_amount;
	}

	public function getTotalAmount() : float
	{
		return $this->total_amount;
	}

	public function getDeliveryInfo() : string
	{
		return $this->delivery_info;
	}

	public function setDeliveryInfo( string $delivery_info ) : void
	{
		$this->delivery_info = $delivery_info;
	}

	public function getDeliveryDelay() : int
	{
		return $this->delivery_delay;
	}

	public function setDeliveryDelay( int $delivery_delay ) : void
	{
		$this->delivery_delay = $delivery_delay;
	}

	public function getDeliveryDate() : Data_DateTime
	{
		return $this->delivery_date;
	}

	public function setDeliveryDate( Data_DateTime $delivery_date ) : void
	{
		$this->delivery_date = $delivery_date;
	}

	public function setDataByCartItem( ShoppingCart_Item $item, int $qty, bool $in_stock ) : void
	{
		$product = $item->getProduct();

		$this->type = Order_Item::ITEM_TYPE_PRODUCT;

		$this->product_id = $product->getId();
		$this->title = $product->getFullName();
		$this->code = $product->getInternalCode();

		$this->available = $in_stock;

		$this->quantity = $qty;
		$this->item_amount = $item->getPricePerItem();
		$this->vat_rate = $product->getVatRate( $item->getCart()->getShop() );
		$this->total_amount = $this->quantity * $this->item_amount;


		//TODO: $this->delivery_info = '';
		//TODO: $this->delivery_delay = 0;
		//TODO: $this->delivery_date = '';
	}

	/**
	 * @param string $value
	 */
	public function setWarehouseCode( string $value ) : void
	{
		$this->warehouse_code = $value;
	}

	/**
	 * @return string
	 */
	public function getWarehouseCode() : string
	{
		return $this->warehouse_code;
	}


}