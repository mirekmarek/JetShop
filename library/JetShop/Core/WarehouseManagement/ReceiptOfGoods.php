<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Logger;
use JetApplication\Currencies;
use JetApplication\Currencies_Currency;
use JetApplication\Entity_Simple;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\NumberSeries_Entity_Trait;
use JetApplication\Product;
use JetApplication\Shops_Shop;
use JetApplication\Supplier;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\WarehouseManagement;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Item;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use Jet\Tr;
use JetApplication\WarehouseManagement_Warehouse;

#[DataModel_Definition(
	name: 'whm_receipt_of_goods',
	database_table_name: 'whm_receipt_of_goods',
)]
class Core_WarehouseManagement_ReceiptOfGoods extends Entity_Simple implements NumberSeries_Entity_Interface, Context_ProvidesContext_Interface
{
	use Context_ProvidesContext_Trait;
	use NumberSeries_Entity_Trait;
	
	public const STATUS_PENDING = 'pending';
	public const STATUS_DONE = 'done';
	public const STATUS_CANCELLED = 'cancelled';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $warehouse_id = 0;
	
	protected WarehouseManagement_Warehouse|null $warehouse = null;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $supplier_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $status = self::STATUS_PENDING;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $order_number = '';
	
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Supplier\'s bill type:'
	)]
	protected string $suppliers_bill_type = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Supplier\'s bill number:'
	)]
	protected string $suppliers_bill_number = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Currency used by the supplier:',
		is_required: false,
		select_options_creator: [
			Currencies::class,
			'getScope'
		],
		error_messages: [
		]
	)]
	protected string $currency_code = '';

	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE,
		label: 'Date:'
	)]
	protected ?Data_DateTime $receipt_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Notes:',
		is_required: false,
		error_messages: [
		]
	)]
	protected string $notes = '';
	
	/**
	 * @var WarehouseManagement_ReceiptOfGoods_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: WarehouseManagement_ReceiptOfGoods_Item::class
	)]
	protected array $items = [];
	
	
	public static function getStatusScope(): array
	{
		return [
			WarehouseManagement_ReceiptOfGoods::STATUS_PENDING   => Tr::_( 'Pending' ),
			WarehouseManagement_ReceiptOfGoods::STATUS_DONE      => Tr::_( 'Done' ),
			WarehouseManagement_ReceiptOfGoods::STATUS_CANCELLED => Tr::_( 'Cancelled' )
		];
	}
	
	
	public function getStatus(): string
	{
		return $this->status;
	}
	
	public function setStatus( string $status ): void
	{
		$this->status = $status;
	}
	
	
	
	
	public function getWarehouseId(): int
	{
		return $this->warehouse_id;
	}
	
	public function setWarehouseId( int $warehouse_id ): void
	{
		$this->warehouse_id = $warehouse_id;
		$this->warehouse = null;
	}
	
	public function getWarehouse() : ?WarehouseManagement_Warehouse
	{
		if(!$this->warehouse) {
			$this->warehouse = WarehouseManagement_Warehouse::get( $this->warehouse_id );
		}
		
		return $this->warehouse;
	}
	
	public function getSupplierId(): int
	{
		return $this->supplier_id;
	}
	
	public function setSupplierId( int $supplier_id ): void
	{
		$this->supplier_id = $supplier_id;
	}
	
	
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->created;
	}
	
	public function getNumberSeriesEntityShop(): ?Shops_Shop
	{
		return null;
	}
	
	
	public function getCurrency() : Currencies_Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getReceiptDate(): ?Data_DateTime
	{
		return $this->receipt_date;
	}
	
	public function setReceiptDate( Data_DateTime|string|null $receipt_date ): void
	{
		$this->receipt_date = Data_DateTime::catchDateTime( $receipt_date );
	}
	
	public function getNotes(): string
	{
		return $this->notes;
	}
	
	public function setNotes( string $notes ): void
	{
		$this->notes = $notes;
	}
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}
	
	public function getOrderNumber(): string
	{
		return $this->order_number;
	}
	
	public function setOrderNumber( string $order_number ): void
	{
		$this->order_number = $order_number;
	}
	
	public function getSuppliersBillType(): string
	{
		return $this->suppliers_bill_type;
	}
	
	public function setSuppliersBillType( string $suppliers_bill_type ): void
	{
		$this->suppliers_bill_type = $suppliers_bill_type;
	}
	
	public function getSuppliersBillNumber(): string
	{
		return $this->suppliers_bill_number;
	}
	
	public function setSuppliersBillNumber( string $suppliers_bill_number ): void
	{
		$this->suppliers_bill_number = $suppliers_bill_number;
	}
	
	
	
	/**
	 * @return WarehouseManagement_ReceiptOfGoods_Item[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function addItem( WarehouseManagement_ReceiptOfGoods_Item $item ): void
	{
		$this->items[] = $item;
	}
	
	public function afterAdd() : void
	{
		parent::afterAdd();
		$this->generateNumber();
	}
	
	public function afterUpdate(): void
	{
		parent::afterUpdate();
	}
	
	public function afterDelete(): void
	{
		parent::afterDelete();
	}
	
	
	public function prepareNew( int $warehouse_id, Supplier $supplier ) : void
	{
		$this->setWarehouseId( $warehouse_id );
		$this->setSupplierId( $supplier->getId() );
		$this->setCurrencyCode( $supplier->getCurrencyCode() );
		$this->setReceiptDate( Data_DateTime::now() );
		
		
		$products = Product::fetchInstances(['supplier_id'=>$supplier->getId()]);
		
		foreach($products as $p) {
			if( !$p->isPhysicalProduct() ) {
				continue;
			}
			
			$item = new WarehouseManagement_ReceiptOfGoods_Item();
			
			/**
			 * @var WarehouseManagement_ReceiptOfGoods $this
			 */
			$item->setupProduct( $this, $p, 0.0 );
			
			$this->items[$item->getProductId()] = $item;
		}
		
	}
	
	public function prepareNewByOrder( int $warehouse_id, Supplier_GoodsOrder $order ) : void
	{
		
		
		$this->setWarehouseId( $warehouse_id );
		$this->setSupplierId( $order->getSupplierId() );
		$this->setCurrencyCode( Supplier::get( $order->getSupplierId() )->getCurrencyCode() );
		$this->setReceiptDate( Data_DateTime::now() );
		$this->setOrderId( $order->getId() );
		$this->setOrderNumber( $order->getNumber() );
		
		
		foreach($order->getItems() as $p) {
			$item = new WarehouseManagement_ReceiptOfGoods_Item();
			
			/**
			 * @var WarehouseManagement_ReceiptOfGoods $this
			 */
			$item->setupOrderItem( $this, $p );
			
			$this->items[$item->getProductId()] = $item;
		}
		
		$products = Product::fetchInstances(['supplier_id'=>$order->getSupplierId()]);
		
		foreach($products as $p) {
			if(isset($this->items[$p->getId()])) {
				continue;
			}
			
			$item = new WarehouseManagement_ReceiptOfGoods_Item();
			
			/**
			 * @var WarehouseManagement_ReceiptOfGoods $this
			 */
			$item->setupProduct( $this, $p, 0.0 );
			
			$this->items[$item->getProductId()] = $item;
		}
		
	}
	
	public function done() : bool
	{
		if( $this->status != static::STATUS_PENDING ) {
			return false;
		}
		
		foreach($this->items as $i=>$item) {
			if(!$item->getUnitsReceived()) {
				$item->delete();
				unset( $this->items[$i] );
			}
		}
		
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $this
		 */
		WarehouseManagement::manageReceiptOfGoods( $this );

		$this->status = static::STATUS_DONE;
		$this->save();
		
		Logger::success(
			event: 'whm_rog_done',
			event_message: 'WHM - Receipt Of Goods '.$this->getNumber().' hus been completed',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
	}
	
	public function cancel() : bool
	{
		if( $this->status != static::STATUS_PENDING ) {
			return false;
		}
		
		$this->status = static::STATUS_CANCELLED;
		$this->save();
		
		Logger::success(
			event: 'whm_rog_cancelled',
			event_message: 'WHM - Receipt Of Goods '.$this->getNumber().' hus been cancelled',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
	}

}