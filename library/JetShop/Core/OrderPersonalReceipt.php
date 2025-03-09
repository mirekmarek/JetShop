<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Fetch_Instances;
use Jet\Form;
use Jet\Form_Field_Textarea;
use Jet\Tr;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_OrderPersonalReceipt;
use JetApplication\Complaint;
use JetApplication\Context;
use JetApplication\Context_HasContext_Interface;
use JetApplication\Context_HasContext_Trait;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Delivery_Method_EShopData;
use JetApplication\EShopEntity_Event;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShop;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\Order;
use JetApplication\OrderPersonalReceipt_Item;
use JetApplication\OrderPersonalReceipt_Status;
use JetApplication\OrderPersonalReceipt_Status_Cancel;
use JetApplication\OrderPersonalReceipt_Status_Canceled;
use JetApplication\OrderPersonalReceipt_Status_HandedOver;
use JetApplication\OrderPersonalReceipt_Status_InProgress;
use JetApplication\OrderPersonalReceipt_Status_Pending;
use JetApplication\OrderPersonalReceipt_Status_Prepared;
use JetApplication\Product_EShopData;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\OrderPersonalReceipt_Event;

#[DataModel_Definition(
	name: 'order_personal_receipt',
	database_table_name: 'order_personal_receipt',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Order personal receipt dispatch',
	admin_manager_interface: Admin_Managers_OrderPersonalReceipt::class
)]
abstract class Core_OrderPersonalReceipt extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasNumberSeries_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface,
	Context_HasContext_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasEvents_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use Context_HasContext_Trait;
	use Context_ProvidesContext_Trait;
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $is_custom = false;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $order_id = 0;
	
	protected Order|null|bool $order;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $warehouse_id = 0;
	
	protected ?WarehouseManagement_Warehouse $warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected ?Data_DateTime $dispatch_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $amount_to_pay = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $service_codes = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $our_note = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	protected int $boxes_count = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE,
	)]
	protected ?Data_DateTime $headed_over_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME,
	)]
	protected ?Data_DateTime $headed_over_date_time = null;
	
	/**
	 * @var OrderPersonalReceipt_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: OrderPersonalReceipt_Item::class
	)]
	protected array $items = [];
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return false;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Order personal receipt';
	}
	
	
	public static function getContextScope() : array
	{
		
		return [
			Order::getProvidesContextType()     => Tr::_( 'Order' ),
			Complaint::getProvidesContextType() => Tr::_( 'Complaint' )
		];
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfToBeCanceled( WarehouseManagement_Warehouse $warehouse ): DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			'status' => static::STATUS_CANCEL,
			'AND',
			'warehouse_id' => $warehouse->getId()
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfPending( WarehouseManagement_Warehouse $warehouse ): DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			'status' => static::STATUS_PENDING,
			'AND',
			'warehouse_id' => $warehouse->getId()
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfInProgress( WarehouseManagement_Warehouse $warehouse ): DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			'status' => static::STATUS_IN_PROGRESS,
			'AND',
			'warehouse_id' => $warehouse->getId()
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListByContext( Context $context ) : DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( $context->getWhere() );
		
		$list->getQuery()->setOrderBy(['-created']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfPrepared( WarehouseManagement_Warehouse $warehouse ) : DataModel_Fetch_Instances|iterable
	{
		$list =  static::fetchInstances( [
			'status' => [
				static::STATUS_PREPARED
			],
			'AND',
			'warehouse_id' => $warehouse->getId()
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	/**
	 * @return DataModel_Fetch_Instances|static[]
	 */
	public static function getListOfHandedOver( WarehouseManagement_Warehouse $warehouse ) : DataModel_Fetch_Instances|iterable
	{
		$now = Data_DateTime::now();
		$now->setOnlyDate( true );
		
		$list =  static::fetchInstances( [
			'status' => [
				static::STATUS_HANDED_OVER
			],
			'AND',
			'warehouse_id' => $warehouse->getId(),
			'AND',
			'headed_over_date' => $now,
		] );
		
		$list->getQuery()->setOrderBy(['created']);
		
		return $list;
	}
	
	
	public static function getByNumber( string $number ) : ?static
	{
		return static::load([
			'number' => $number
		]);
	}
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		$this->generateNumber();
	}
	
	public function getIsCustom(): bool
	{
		return $this->is_custom;
	}
	
	public function setIsCustom( bool $is_custom ): void
	{
		$this->is_custom = $is_custom;
	}
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->getDispatchDate();
	}
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return $this->getEshop();
	}
	
	
	public function getWarehouseId(): int
	{
		return $this->warehouse_id;
	}
	
	public function setWarehouseId( int $warehouse_id ): void
	{
		$this->warehouse_id = $warehouse_id;
	}
	
	public function getWarehouse() : WarehouseManagement_Warehouse
	{
		if(!$this->warehouse) {
			$this->warehouse = WarehouseManagement_Warehouse::get( $this->getWarehouseId() );
		}
		
		return $this->warehouse;
	}
	
	public function getOrderId(): int
	{
		return $this->order_id;
	}
	
	public function getOrder() : ?Order
	{
		if($this->order===null) {
			$this->order = Order::get( $this->order_id );
			if(!$this->order) {
				$this->order = false;
			}
		}
		
		return $this->order?:null;
	}
	
	public function setOrderId( int $order_id ): void
	{
		$this->order_id = $order_id;
	}
	
	
	public function getDispatchDate(): ?Data_DateTime
	{
		return $this->dispatch_date;
	}
	
	public function setDispatchDate( ?Data_DateTime $dispatch_date ): void
	{
		$this->dispatch_date = $dispatch_date;
	}
	
	public function getHeadedOverDate(): ?Data_DateTime
	{
		return $this->headed_over_date;
	}
	
	public function getHeadedOverDateTime(): ?Data_DateTime
	{
		return $this->headed_over_date_time;
	}
	
	
	
	public function getAmountToPay(): float
	{
		return $this->amount_to_pay;
	}
	
	public function setAmountToPay( float $value ): void
	{
		$this->amount_to_pay = $value;
	}
	
	
	public function getCurrency(): Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	public function setCurrency( Currency $currency ): void
	{
		$this->currency_code = $currency->getCode();
	}
	
	
	public function getServiceCodes(): string
	{
		return $this->service_codes;
	}
	
	public function setServiceCodes( string $service_codes ): void
	{
		$this->service_codes = $service_codes;
	}
	
	public function getOurNote(): string
	{
		return $this->our_note;
	}
	
	public function setOurNote( string $our_note ): void
	{
		$this->our_note = $our_note;
	}
	
	public function getBoxesCount(): int
	{
		return $this->boxes_count;
	}
	
	public function setBoxesCount( int $boxes_count ): void
	{
		$this->boxes_count = $boxes_count;
	}
	
	/**
	 * @return OrderPersonalReceipt_Item[]
	 */
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function addItem( OrderPersonalReceipt_Item $item ) : void
	{
		$this->items[] = $item;
	}
	
	public function setWarehouse( WarehouseManagement_Warehouse $warehouse ) : void
	{
		$this->setWarehouseId( $warehouse->getId() );
		$this->warehouse = $warehouse;
		
		foreach($this->items as $item) {
			$wh_card = $warehouse->getCard( $item->getProductId() );
			
			$item->setWarehouseSector( $wh_card->getSector() );
			$item->setWarehouseRack( $wh_card->getRack() );
			$item->setWarehousePosition( $wh_card->getPosition() );
			
		}
	}
	
	
	public static function newByComplaint( Complaint $complaint, int $product_id, int $delivery_method, string $delivery_point_code ) : static
	{
		$_order = Order::get($complaint->getOrderId());
		$product = Product_EShopData::get( $product_id, $complaint->getEshop() );
		
		$delivery_method = Delivery_Method_EShopData::get( $delivery_method, $complaint->getEshop() );
		
		
		$dispatch = new static();
		
		$dispatch->setDispatchDate( Data_DateTime::now() );
		
		$dispatch->setEshop( $complaint->getEshop() );
		$dispatch->setContext( $complaint->getProvidesContext() );
		$dispatch->setOrderId( $complaint->getOrderId() );
		
		$dispatch->setWarehouse( $_order->getWarehouse() );
		
		$dispatch->setCurrency( $_order->getCurrency() );
		
		$dispatch_item = new OrderPersonalReceipt_Item();
		$dispatch_item->setProductId( $product->getId() );
		$dispatch_item->setTitle( $product->getName() );
		$dispatch_item->setNumberOfUnits( 1, $product->getKind()?->getMeasureUnit() );
		$dispatch_item->setInternalCode( $product->getInternalCode() );
		$dispatch_item->setEAN( $product->getEan() );
		
		$dispatch->items[] = $dispatch_item;
		$dispatch->save();
		
		$dispatch->setStatus( OrderPersonalReceipt_Status_Pending::get() );
		
		return $dispatch;
	}
	
	public static function newByOrder( Order $order ) : static
	{
		
		$dispatch = new static();
		
		$dispatch->setWarehouse( $order->getWarehouse() );
		$dispatch->setDispatchDate( Data_DateTime::now() );
		$dispatch->setEshop( $order->getEshop() );
		$dispatch->setContext( $order->getProvidesContext() );
		$dispatch->setOrderId( $order->getId() );
		
		
		if($order->getPaymentMethod()->getKind()->isCOD()) {
			$dispatch->setAmountToPay( $order->getTotalAmount_WithVAT() );
		}
		$dispatch->setCurrency( $order->getCurrency() );
		
		
		$delivery_method = $order->getDeliveryMethod();
		
		foreach($order->getItems() as $order_item) {
			if( $order_item->isPhysicalProduct() ) {
				$product = Product_EShopData::get( $order_item->getItemId(), $order->getEshop() );
				
				$dispatch_item = new OrderPersonalReceipt_Item();
				$dispatch_item->setProductId( $order_item->getItemId() );
				$dispatch_item->setTitle( $order_item->getTitle() );
				$dispatch_item->setNumberOfUnits( $order_item->getNumberOfUnits(), $order_item->getMeasureUnit() );
				$dispatch_item->setInternalCode( $product->getInternalCode() );
				$dispatch_item->setEAN( $product->getEan() );
				
				
				$dispatch->items[] = $dispatch_item;
			}
		}
		
		
		$dispatch->save();
		
		$dispatch->setStatus( OrderPersonalReceipt_Status_Pending::get() );
		
		return $dispatch;
	}
	
	public function getAdminTitle(): string
	{
		return $this->number;
	}
	
	
	public function isEditable() : bool
	{
		$status = $this->getStatus();
		/**
		 * @var OrderPersonalReceipt_Status $status
		 */
		
		return $status::isEditable();
	}
	
	
	public function isPrepared() : bool
	{
		return $this->status==OrderPersonalReceipt_Status_Prepared::getCode();
	}
	
	public function preparationStarted() : void
	{
		$this->setStatus( OrderPersonalReceipt_Status_InProgress::get() );
	}
	
	
	
	public function prepared() : void
	{
		$this->setStatus( OrderPersonalReceipt_Status_Prepared::get() );
	}
	
	
	
	public function rollBack() : bool
	{
		//TODO: virtual status
		$this->setStatus(OrderPersonalReceipt_Status_Pending::get(), handle_event: false );
		
		$this->headed_over_date = null;
		$this->headed_over_date_time = null;
		$this->save();
		
		return true;
	}
	
	public function cancel() : void
	{
		$this->setStatus( OrderPersonalReceipt_Status_Cancel::get() );
	}
	
	public function confirmCancellation() : void
	{
		$this->setStatus( OrderPersonalReceipt_Status_Canceled::get() );
	}
	
	
	public function handedOver() : void
	{
		$this->setStatus( OrderPersonalReceipt_Status_HandedOver::get() );
		
		$this->headed_over_date = Data_DateTime::now();
		$this->headed_over_date_time = Data_DateTime::now();
		$this->save();
	}
	
	public function getOurNoteForm() : Form
	{
		$note = new Form_Field_Textarea('our_note', '');
		$note->setDefaultValue( $this->our_note );
		$note->setFieldValueCatcher( function( $value ) {
			$this->our_note = $value;
			$this->save();
		} );
		
		$form = new Form('our_note_form', [$note]);
		return $form;
	}
	
	
	public function setEditable( bool $editable ): void
	{
	}
	
	public function getAddForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchAddForm(): bool
	{
		return false;
	}
	
	public function getEditForm(): Form
	{
		return new Form('', []);
	}
	
	public function catchEditForm(): bool
	{
		return false;
	}
	
	public static function getStatusList(): array
	{
		return OrderPersonalReceipt_Status::getList();
	}
	
	public function createEvent( EShopEntity_Event|OrderPersonalReceipt_Event $event ): OrderPersonalReceipt_Event
	{
		$event->init( $this->getEshop() );
		$event->setOrderPersonalReceipt( $this );
		
		return $event;
	}
	
}
