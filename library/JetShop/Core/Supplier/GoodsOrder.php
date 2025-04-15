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
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Float;
use Jet\Form_Field_Input;
use Jet\Locale;
use Jet\Logger;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_SupplierGoodsOrders;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\Product;
use JetApplication\EShop;
use JetApplication\Supplier;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\Supplier_GoodsOrder_Item;
use JetApplication\Supplier_GoodsOrder_Status;
use JetApplication\Supplier_GoodsOrder_Status_Cancelled;
use JetApplication\Supplier_GoodsOrder_Status_Pending;
use JetApplication\Supplier_GoodsOrder_Status_GoodsReceived;
use JetApplication\Supplier_GoodsOrder_Status_ProblemDuringSending;
use JetApplication\Supplier_GoodsOrder_Status_SentToSupplier;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_Warehouse;


#[DataModel_Definition(
	name: 'supplier_goods_order',
	database_table_name: 'supplier_goods_orders',
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Goods order',
	admin_manager_interface: Admin_Managers_SupplierGoodsOrders::class
)]
abstract class Core_Supplier_GoodsOrder extends EShopEntity_Basic implements
	EShopEntity_HasNumberSeries_Interface,
	EShopEntity_Admin_Interface,
	EShopEntity_HasGet_Interface,
	EShopEntity_HasStatus_Interface
{
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_HasStatus_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Destination warehouse:',
		is_required: false,
		select_options_creator: [
			WarehouseManagement_Warehouse::class,
			'getScope'
		],
		error_messages: [
		]
	)]
	protected int $destination_warehouse_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $number_by_supplier = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $bill_number = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	protected string $problem_during_sending_error = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected ?Data_DateTime $order_created_date = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected ?Data_DateTime $goods_received_date = null;
	

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $supplier_id = 0;
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_company_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_company_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_company_vat_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_address_street_and_no = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_address_town = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_address_zip = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_address_country = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_phone_1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_phone_2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_email_1 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $supplier_email_2 = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_LOCALE
	)]
	protected ?Locale $locale = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $currency_exchange_rate = 1.0;
	
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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_DATE,
		label: 'Expected delivery date:',
		is_required: false,
		error_messages: [
		]
	)]
	protected ?Data_DateTime $expected_delivery_date = null;
	
	/**
	 * @var Supplier_GoodsOrder_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Supplier_GoodsOrder_Item::class
	)]
	protected array $items = [];
	
	protected static array $flags = [];
	
	public static function getStatusList(): array
	{
		return Supplier_GoodsOrder_Status::getList();
	}
	
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return false;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Orders of goods from suppliers';
	}
	
	
	public function getDestinationWarehouseId(): int
	{
		return $this->destination_warehouse_id;
	}
	
	public function setDestinationWarehouseId( int $destination_warehouse_id ): void
	{
		$this->destination_warehouse_id = $destination_warehouse_id;
	}
	
	
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		$this->generateNumber();
		$this->setStatus( Supplier_GoodsOrder_Status_Pending::get() );
	}
	
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->getOrderCreatedDate();
	}
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return null;
	}
	
	public static function getByNumber( string $number ) : ?static
	{
		return static::load(['number'=>$number]);
	}
	
	public function getOrderCreatedDate(): ?Data_DateTime
	{
		return $this->order_created_date;
	}
	
	public function setOrderCreatedDate( Data_DateTime|string|null $order_created_date ): void
	{
		$this->order_created_date = Data_DateTime::catchDateTime($order_created_date);
	}
	
	public function getSupplierId(): int
	{
		return $this->supplier_id;
	}
	
	public function setSupplierId( int $supplier_id ): void
	{
		$this->supplier_id = $supplier_id;
	}
	
	public function getSupplier() : ?Supplier
	{
		return Supplier::get( $this->supplier_id );
	}
	
	public function getNumberBySupplier(): string
	{
		return $this->number_by_supplier;
	}
	
	public function setNumberBySupplier( string $number_by_supplier ): void
	{
		$this->number_by_supplier = $number_by_supplier;
	}
	
	public function getBillNumber(): string
	{
		return $this->bill_number;
	}
	
	public function setBillNumber( string $bill_number ): void
	{
		$this->bill_number = $bill_number;
	}
	
	public function getProblemDuringSendingError(): string
	{
		return $this->problem_during_sending_error;
	}
	
	public function setProblemDuringSendingError( string $problem_during_sending_error ): void
	{
		$this->problem_during_sending_error = $problem_during_sending_error;
	}
	
	public function getGoodsReceivedDate(): ?Data_DateTime
	{
		return $this->goods_received_date;
	}
	
	public function setGoodsReceivedDate( Data_DateTime|string|null $goods_received_date ): void
	{
		$this->goods_received_date = Data_DateTime::catchDateTime($goods_received_date);
	}
	
	public function getSupplierCompanyName(): string
	{
		return $this->supplier_company_name;
	}
	
	public function setSupplierCompanyName( string $supplier_company_name ): void
	{
		$this->supplier_company_name = $supplier_company_name;
	}
	
	public function getSupplierCompanyId(): string
	{
		return $this->supplier_company_id;
	}
	
	public function setSupplierCompanyId( string $supplier_company_id ): void
	{
		$this->supplier_company_id = $supplier_company_id;
	}
	
	public function getSupplierCompanyVatId(): string
	{
		return $this->supplier_company_vat_id;
	}
	
	public function setSupplierCompanyVatId( string $supplier_company_vat_id ): void
	{
		$this->supplier_company_vat_id = $supplier_company_vat_id;
	}
	
	public function getSupplierAddressStreetAndNo(): string
	{
		return $this->supplier_address_street_and_no;
	}
	
	public function setSupplierAddressStreetAndNo( string $supplier_address_street_and_no ): void
	{
		$this->supplier_address_street_and_no = $supplier_address_street_and_no;
	}
	
	public function getSupplierAddressTown(): string
	{
		return $this->supplier_address_town;
	}
	
	public function setSupplierAddressTown( string $supplier_address_town ): void
	{
		$this->supplier_address_town = $supplier_address_town;
	}
	
	public function getSupplierAddressZip(): string
	{
		return $this->supplier_address_zip;
	}
	
	public function setSupplierAddressZip( string $supplier_address_zip ): void
	{
		$this->supplier_address_zip = $supplier_address_zip;
	}
	
	public function getSupplierAddressCountry(): string
	{
		return $this->supplier_address_country;
	}
	
	public function setSupplierAddressCountry( string $supplier_address_country ): void
	{
		$this->supplier_address_country = $supplier_address_country;
	}
	
	public function getSupplierPhone1(): string
	{
		return $this->supplier_phone_1;
	}
	
	public function setSupplierPhone1( string $supplier_phone_1 ): void
	{
		$this->supplier_phone_1 = $supplier_phone_1;
	}
	
	public function getSupplierPhone2(): string
	{
		return $this->supplier_phone_2;
	}
	
	public function setSupplierPhone2( string $supplier_phone_2 ): void
	{
		$this->supplier_phone_2 = $supplier_phone_2;
	}
	
	public function getSupplierEmail1(): string
	{
		return $this->supplier_email_1;
	}
	
	public function setSupplierEmail1( string $supplier_email_1 ): void
	{
		$this->supplier_email_1 = $supplier_email_1;
	}
	
	public function getSupplierEmail2(): string
	{
		return $this->supplier_email_2;
	}
	
	public function setSupplierEmail2( string $supplier_email_2 ): void
	{
		$this->supplier_email_2 = $supplier_email_2;
	}
	
	public function getLocale(): ?Locale
	{
		return $this->locale;
	}
	
	public function setLocale( ?Locale $locale ): void
	{
		$this->locale = $locale;
	}
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getCurrencyExchangeRate(): float
	{
		return $this->currency_exchange_rate;
	}
	
	public function setCurrencyExchangeRate( float $currency_exchange_rate ): void
	{
		$this->currency_exchange_rate = $currency_exchange_rate;
	}
	
	public function getExpectedDeliveryDate(): ?Data_DateTime
	{
		return $this->expected_delivery_date;
	}
	
	public function setExpectedDeliveryDate( Data_DateTime|string|null $expected_delivery_date ): void
	{
		$this->expected_delivery_date = Data_DateTime::catchDate( $expected_delivery_date );
	}
	
	
	
	public function getNotes(): string
	{
		return $this->notes;
	}
	
	public function setNotes( string $notes ): void
	{
		$this->notes = $notes;
	}
	
	public function getItems(): array
	{
		return $this->items;
	}
	
	public function addItem( Supplier_GoodsOrder_Item $item ) : void
	{
		$this->items[$item->getProductId()] = $item;
	}
	
	public static function prepareNew( Supplier $supplier ) : static
	{
		$new = new static();
		
		$new->order_created_date = Data_DateTime::now();
		$new->supplier_id = $supplier->getId();
		$new->supplier_company_name = $supplier->getCompanyName();
		$new->supplier_company_id = $supplier->getCompanyId();
		$new->supplier_company_vat_id = $supplier->getCompanyVatId();
		$new->supplier_address_street_and_no = $supplier->getAddressStreetAndNo();
		$new->supplier_address_town = $supplier->getAddressTown();
		$new->supplier_address_zip = $supplier->getAddressZip();
		$new->supplier_address_country = $supplier->getAddressCountry();
		$new->supplier_phone_1 = $supplier->getPhone1();
		$new->supplier_phone_2 = $supplier->getPhone2();
		$new->supplier_email_1 = $supplier->getEmail1();
		$new->supplier_email_2 = $supplier->getEmail2();
		$new->locale = $supplier->getLocale();
		$new->currency_code = $supplier->getCurrencyCode();
		
		$products = Product::fetchInstances(['supplier_id'=>$supplier->getId()]);
		
		foreach($products as $p) {
			if( !$p->isPhysicalProduct() ) {
				continue;
			}
			$item = new Supplier_GoodsOrder_Item();
			
			/**
			 * @var Supplier_GoodsOrder $new
			 */
			$item->setupProduct( $new, $p, null );
			
			$new->items[$item->getProductId()] = $item;
		}

		return $new;
	}
	
	public function send() : bool
	{
		//TODO:
		foreach($this->items as $i=>$item) {
			if(!$item->getUnitsOrdered()) {
				$item->delete();
				unset($this->items[$i]);
			}
		}
		
		/**
		 * @var Supplier_GoodsOrder $this
		 */
		$error_message = '';
		
		if( !$this->getSupplier()->getBackendModule()->sendOrder( $this, $error_message ) ) {
			$this->setStatus( Supplier_GoodsOrder_Status_ProblemDuringSending::get() );

			$this->problem_during_sending_error = $error_message;
			$this->save();
			
			return false;
		}
		
		$this->setStatus( Supplier_GoodsOrder_Status_SentToSupplier::get() );
		$this->problem_during_sending_error = '';
		$this->save();
		
		Logger::success(
			event: 'supplier_order_send',
			event_message: 'Supplier order '.$this->getNumber().' hus been sent',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
		
	}
	
	public function cancel() : bool
	{
		$this->setStatus( Supplier_GoodsOrder_Status_Cancelled::get() );
		
		$this->problem_during_sending_error = '';
		$this->save();
		
		Logger::success(
			event: 'supplier_order_cancelled',
			event_message: 'Supplier order '.$this->getNumber().' hus been cancelled',
			context_object_id: $this->getId(),
			context_object_name: $this->getNumber(),
			context_object_data: $this
		);
		
		return true;
		
	}
	
	public function received( WarehouseManagement_ReceiptOfGoods $rcp ) : void
	{
		foreach($rcp->getItems() as $rcp_item) {
			$order_item = $this->items[$rcp_item->getProductId()] ?? null;
			if(!$order_item) {
				continue;
			}
			
			$order_item->setUnitsReceived( $order_item->getUnitsReceived() + $rcp_item->getUnitsReceived() );
			$order_item->save();
		}
		
		$this->setGoodsReceivedDate( $rcp->getReceiptDate() );
		
		$this->setStatus( Supplier_GoodsOrder_Status_GoodsReceived::get() );
		
		$this->save();
		
	}
	
	/**
	 * @param int $warehouse_id
	 * @return static[]
	 */
	public static function getSentForWarehouse( int $warehouse_id ) : array
	{
		$orders = Supplier_GoodsOrder::fetch([''=>[
			'status' => Supplier_GoodsOrder_Status_SentToSupplier::CODE,
			'AND',
			'destination_warehouse_id' => $warehouse_id
		]]);
		
		return $orders;
	}
	
	protected function setupForm( Form $form ) : void
	{
		foreach($this->items as $item) {
			$qty = new Form_Field_Float( '/order/'.$item->getProductId(), '' );
			$qty->setDefaultValue( $item->getUnitsOrdered() );
			$qty->setFieldValueCatcher( function( float $v ) use ($item) : void {
				$item->setUnitsOrdered( $v );
			} );
			$form->addField( $qty );
		}
	}
	
	public function getAdminTitle() : string
	{
		return $this->supplier_company_name . ' / '.$this->number;
	}
	
	protected function setupAddForm( Form $form ) : void
	{
		$this->setupForm( $form );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$this->setupForm( $form );
		
		if(!$this->getStatus()->orderCanBeUpdated()) {
			$form->setIsReadonly();
		}
	}
	
	protected function catchForm( Form $form ) : bool
	{
		if(!$form->catch()) {
			return false;
		}
		
		$everything_zero = true;
		foreach($this->getItems() as $item) {
			if($item->getUnitsOrdered()>0) {
				$everything_zero = false;
				break;
			}
		}
		
		if($everything_zero) {
			$form->setCommonMessage(
				UI_messages::createDanger(Tr::_('Please specify at least one item to order'))
			);
			return false;
		}
		
		return true;
	}
	
	public function catchAddForm() : bool
	{
		return $this->catchForm( $this->getAddForm() );
	}
	
	public function catchEditForm() : bool
	{
		return $this->catchForm( $this->getEditForm() );
	}
	
	public function getSetSupplierOrderNumberForm() : Form
	{
		$number = new Form_Field_Input('number', 'Order number: ');
		$number->setDefaultValue( $this->getNumberBySupplier() );
		$number->setFieldValueCatcher( function( string $value ) {
			$this->setNumberBySupplier( $value );
		} );
		
		$form = new Form('set_supplier_order_number', [$number]);
		
		return $form;
	}
	
}