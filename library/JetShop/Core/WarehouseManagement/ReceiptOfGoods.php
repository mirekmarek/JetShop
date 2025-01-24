<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use Jet\Logger;
use Jet\UI_messages;
use JetApplication\Admin_Entity_Simple_Interface;
use JetApplication\Admin_Entity_Simple_Trait;
use JetApplication\Admin_Managers_ReceiptOfGoods;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Entity_Simple;
use JetApplication\JetShopEntity_Definition;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\NumberSeries_Entity_Trait;
use JetApplication\Product;
use JetApplication\EShop;
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
#[JetShopEntity_Definition(
	admin_manager_interface: Admin_Managers_ReceiptOfGoods::class
)]
abstract class Core_WarehouseManagement_ReceiptOfGoods extends Entity_Simple implements
	NumberSeries_Entity_Interface,
	Context_ProvidesContext_Interface,
	Admin_Entity_Simple_Interface
{
	use Context_ProvidesContext_Trait;
	use NumberSeries_Entity_Trait;
	use Admin_Entity_Simple_Trait;
	
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
	
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return false;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Warehouse management - receipt of goods';
	}
	
	
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
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return null;
	}
	
	
	public function getCurrency() : Currency
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
	
	
	public function getAdminTitle() : string
	{
		return $this->number;
	}
	
	protected function setupForm( Form $form ) : void
	{
		foreach($this->items as $p_id=>$item) {
			$qty = new Form_Field_Float( '/item_'.$p_id.'/qty', '' );
			$qty->setDefaultValue( $item->getUnitsReceived() );
			$qty->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setUnitsReceived( $v );
			} );
			$form->addField( $qty );
			
			$ppu = new Form_Field_Float( '/item_'.$p_id.'/price_per_unit', '' );
			$ppu->setDefaultValue( $item->getPricePerUnit() );
			$ppu->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setPricePerUnit( $v );
			} );
			$form->addField( $ppu );
			
			$total_price = new Form_Field_Float( '/item_'.$p_id.'/total_price', '' );
			$total_price->setDefaultValue( $item->getTotalPrice() );
			$total_price->setFieldValueCatcher( function( float $v ) use ($item) : void {
			} );
			$form->addField( $total_price );
			
			$sector = new Form_Field_Input( '/item_'.$p_id.'/sector', '' );
			$sector->setDefaultValue( $item->getSector() );
			$sector->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setSector( $v );
			} );
			$form->addField( $sector );
			
			
			$rack = new Form_Field_Input( '/item_'.$p_id.'/rack', '' );
			$rack->setDefaultValue( $item->getRack() );
			$rack->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setRack( $v );
			} );
			$form->addField( $rack );
			
			
			$position = new Form_Field_Input( '/item_'.$p_id.'/position', '' );
			$position->setDefaultValue( $item->getPosition() );
			$position->setFieldValueCatcher( function( string $v ) use ($item) : void {
				$item->setPosition( $v );
			} );
			$form->addField( $position );
		}
	}
	
	protected function catchForm( Form $form ) : bool
	{
		if(!$form->catch()) {
			return false;
		}
		
		$everything_zero = true;
		foreach($this->getItems() as $item) {
			if($item->getUnitsReceived()>0) {
				$everything_zero = false;
				break;
			}
		}
		
		if($everything_zero) {
			$form->setCommonMessage(
				UI_messages::createDanger(Tr::_('Please specify at least one item'))
			);
			return false;
		}
		
		return true;
	}
	
	
	
	public function setupAddForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	
	
	public function catchAddForm() : bool
	{
		return $this->catchForm( $this->getAddForm() );
	}
	
	public function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
		
		$order_number = new Form_Field_Input('order_number', 'Order number:');
		$order_number->setDefaultValue( $this->order_number );
		$order_number->setErrorMessages([
			'unknown_order' => 'Unknown order'
		]);
		$order_number->setValidator( function() use ($order_number) {
			$number = $order_number->getValue();
			if(!$number) {
				return true;
			}
			
			$order = Supplier_GoodsOrder::getByNumber( $number );
			if( !$order ) {
				$order_number->setError('unknown_order');
				return false;
			}
			
			return true;
		} );
		$order_number->setFieldValueCatcher( function( string $value ) : void {
			if($value) {
				$order = Supplier_GoodsOrder::getByNumber( $value );
				
				$this->order_id = $order->getId();
				$this->order_number = $order->getNumber();
			} else {
				$this->order_id = 0;
				$this->order_number = '';
			}
		} );
		$form->addField($order_number);
		
		if($this->getStatus()!=static::STATUS_PENDING) {
			$form->setIsReadonly();
		}
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
}