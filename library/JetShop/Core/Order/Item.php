<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;

use JetApplication\Delivery_Method_ShopData;
use JetApplication\Discounts_Discount;
use JetApplication\Order;
use JetApplication\Order_Item;
use JetApplication\Order_Item_SetItem;
use JetApplication\Payment_Method_ShopData;
use JetApplication\ShoppingCart_Item;

#[DataModel_Definition(
	name: 'order_item',
	database_table_name: 'orders_items',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Order::class
)]
abstract class Core_Order_Item extends DataModel_Related_1toN {

	public const ITEM_TYPE_PRODUCT = 'product';
	public const ITEM_TYPE_SERVICE = 'service';
	public const ITEM_TYPE_GIFT = 'gift';
	public const ITEM_TYPE_PAYMENT = 'payment';
	public const ITEM_TYPE_DELIVERY = 'delivery';
	public const ITEM_TYPE_DISCOUNT = 'discount';


	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $order_id = 0;

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
	protected string $item_code = '';

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
	protected int $item_id = 0;

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
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $set_discount_amount = 0.0;
	
	/**
	 * @var Order_Item_SetItem[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Order_Item_SetItem::class
	)]
	protected array $set_items = [];

	
	public function getArrayKeyValue(): string
	{
		return $this->id;
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

	public function getItemCode() : string
	{
		return $this->item_code;
	}

	public function setItemCode( string $item_code ) : void
	{
		$this->item_code = $item_code;
	}

	public function getSubCode() : string
	{
		return $this->sub_code;
	}

	public function setSubCode( string $sub_code ) : void
	{
		$this->sub_code = $sub_code;
	}

	public function getItemId() : string
	{
		return $this->item_id;
	}

	public function setItemId( string $item_id ) : void
	{
		$this->item_id = $item_id;
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
	
	public function getSetDiscountAmount(): float
	{
		return $this->set_discount_amount;
	}
	
	public function setSetDiscountAmount( float $set_discount_amount ): void
	{
		$this->set_discount_amount = $set_discount_amount;
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

	public function setupByCartItem( ShoppingCart_Item $item, int $qty, bool $in_stock ) : void
	{
		$product = $item->getProduct();

		$this->setType( Order_Item::ITEM_TYPE_PRODUCT );

		$this->setItemId( $product->getId() );
		$this->setTitle( $product->getFullName() );
		$this->setItemCode( $product->getInternalCode() );
		$this->setAvailable( $in_stock );

		$this->setQuantity( $qty );
		$this->setItemAmount( $product->getPrice() );
		$this->setVatRate( $product->getVatRate() );
		
		
		if( $product->isSet() ) {
			$set_discount = $product->getSetDiscountAmount();
			$this->setSetDiscountAmount( $set_discount );
			
			foreach( $product->getSetItems() as $set_item ) {
				$order_item_set_item = new Order_Item_SetItem();
				$order_item_set_item->setupBySetItem( $product, $set_item, $qty, $in_stock );
				$this->set_items[] = $order_item_set_item;
			}
		}


		//TODO: $this->delivery_info = '';
		//TODO: $this->delivery_delay = 0;
		//TODO: $this->delivery_date = '';
	}
	
	public function setupByDeliveryMethod( Delivery_Method_ShopData $delivery_method ) : void
	{
		$this->setType( Order_Item::ITEM_TYPE_DELIVERY );
		$this->setItemId( $delivery_method->getId() );
		$this->setItemCode( $delivery_method->getInternalCode() );
		
		$this->setAvailable( true );
		$this->setQuantity( 1 );
		
		$this->setTitle( $delivery_method->getTitle() );
		
		$this->setItemAmount( $delivery_method->getPrice() );
		$this->setVatRate( $delivery_method->getVatRate() );
		
		$this->setDescription( $delivery_method->getDescriptionShort() );
		
	}
	
	public function setupByPaymentMethod( Payment_Method_ShopData $payment_method ) : void
	{
		$this->setType( Order_Item::ITEM_TYPE_PAYMENT );
		$this->setItemCode( $payment_method->getId() );
		$this->setItemCode( $payment_method->getInternalCode() );
		
		$this->setAvailable( true );
		$this->setQuantity( 1 );
		
		$this->setTitle( $payment_method->getTitle() );
		
		$this->setItemAmount( $payment_method->getPrice() );
		$this->setVatRate( $payment_method->getVatRate() );
		
		$this->setDescription( $payment_method->getDescriptionShort() );
		
	}
	
	public function setupByDiscount( Discounts_Discount $discount ) : void
	{
		$this->setType( Order_Item::ITEM_TYPE_DISCOUNT );
		$this->setSubType( $discount->getDiscountType() );
		$this->setItemCode( $discount->getDiscountModule() );
		$this->setSubCode( $discount->getDiscountContext() );
		
		$this->setAvailable( true );
		$this->setQuantity( 1 );
		
		$this->setTitle( $discount->getDescription() );
		$this->setItemAmount( $discount->getAmount() );
		$this->setVatRate( $discount->getVatRate() );
		
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
	
	/**
	 * @return Order_Item_SetItem[]
	 */
	public function getSetItems(): array
	{
		return $this->set_items;
	}

	

}