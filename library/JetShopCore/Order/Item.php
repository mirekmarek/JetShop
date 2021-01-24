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

	const DISCOUNT_TYPE_PRODUCTS = 'disc_products';
	const DISCOUNT_TYPE_SERVICE = 'disc_service';
	const DISCOUNT_TYPE_DELIVERY = 'disc_delivery';
	const DISCOUNT_TYPE_PAYMENT = 'disc_payment';
	const DISCOUNT_TYPE_TOTAL = 'disc_total';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $order_id = 0;

	protected ?Order $__order = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
		form_field_type: false
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $sub_type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $sub_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $product_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
		form_field_type: false
	)]
	protected bool $in_stock = false;

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
	protected float $price_per_item = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $vat_rate = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_price = 0.0;

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

	public function isInStock() : bool
	{
		return $this->in_stock;
	}

	public function setInStock( bool $in_stock ) : void
	{
		$this->in_stock = $in_stock;
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
		$this->total_price = $this->price_per_item*$this->quantity;
	}

	public function getPricePerItem() : float
	{
		return $this->price_per_item;
	}

	public function setPricePerItem( float $price_per_item ) : void
	{
		$this->price_per_item = $price_per_item;
		$this->total_price = $price_per_item*$this->quantity;
	}

	public function getVatRate() : float
	{
		return $this->vat_rate;
	}

	public function setVatRate( float $vat_rate ) : void
	{
		$this->vat_rate = $vat_rate;
	}

	public function getTotalPrice() : float
	{
		return $this->total_price;
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

		$shop_id = $item->getCart()->getShopId();

		$product = $item->getProduct();

		$this->type = Order_Item::ITEM_TYPE_PRODUCT;

		$this->product_id = $product->getId();
		$this->title = $product->getFullName();
		$this->code = $product->getInternalCode();

		$this->in_stock = $in_stock;

		$this->quantity = $qty;
		$this->price_per_item = $item->getPricePerItem();
		$this->vat_rate = $product->getVatRate( $shop_id );
		$this->total_price = $this->quantity * $this->price_per_item;


		//TODO: $this->delivery_info = '';
		//TODO: $this->delivery_delay = 0;
		//TODO: $this->delivery_date = '';
	}


}