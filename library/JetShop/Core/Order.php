<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Availabilities;
use JetApplication\Availabilities_Availability;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Currencies;
use JetApplication\Currencies_Currency;
use JetApplication\Customer_Address;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Marketing_ConversionSourceDetector_Source;
use JetApplication\NumberSeries_Entity_Interface;
use JetApplication\NumberSeries_Entity_Trait;
use JetApplication\Order_Item;
use JetApplication\Order_ProductOverviewItem;
use JetApplication\Order_Trait_Events;
use JetApplication\Order_Trait_Status;
use JetApplication\Order_Trait_Changes;
use JetApplication\Payment_Method_ShopData;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Pricelists;
use JetApplication\Pricelists_Pricelist;
use JetApplication\Product_ShopData;
use JetApplication\Shops_Shop;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\Context_ProvidesContext_Trait;

#[DataModel_Definition(
	name: 'order',
	database_table_name: 'orders',
	key: [
		'name' => 'key',
		'property_names' => [
			'key'
		],
		'type' => DataModel::KEY_TYPE_UNIQUE
	]
)]
abstract class Core_Order extends Entity_WithShopRelation implements NumberSeries_Entity_Interface, Context_ProvidesContext_Interface
{
	
	
	use Context_ProvidesContext_Trait;
	use NumberSeries_Entity_Trait;
	
	use Order_Trait_Events;
	use Order_Trait_Changes;
	use Order_Trait_Status;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $split_source_order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $joined_with_order_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $key = '';

	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $currency_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $pricelist_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 50,
	)]
	protected string $availability_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $conversion_source = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $import_source = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $import_remote_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $ip_address = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_purchased = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $customer_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $email = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $phone = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_company_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $billing_company_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50
	)]
	protected string $billing_company_vat_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_first_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_surname = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_address_street_no = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_address_town = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_address_zip = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_address_country = '';


	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_company_name = '';


	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_first_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_surname = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_address_street_no = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_address_town = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_address_zip = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_address_country = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $special_requirements = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $different_delivery_address = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $company_order = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $newsletter_accepted = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $survey_disagreement = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $delivery_method_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $delivery_personal_takeover_delivery_point_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT
	)]
	protected int $payment_method_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $payment_method_specification = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $product_amount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $delivery_amount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $payment_amount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $service_amount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $discount_amount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $warehouse_id = 0;
	
	protected ?WarehouseManagement_Warehouse $warehouse = null;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $total_weight_of_products = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT,
	)]
	protected float $total_volume_of_products = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE
	)]
	protected Data_DateTime|null $promised_delivery_date = null;
	

	
	/**
	 * @var Order_Item[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Order_Item::class
	)]
	protected array $items = [];


	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}
	
	public function getSplitSourceOrderId(): int
	{
		return $this->split_source_order_id;
	}
	
	public function setSplitSourceOrderId( int $split_source_order_id ): void
	{
		$this->split_source_order_id = $split_source_order_id;
	}
	
	public function getJoinedWithOrderId(): int
	{
		return $this->joined_with_order_id;
	}
	
	public function setJoinedWithOrderId( int $joined_with_order_id ): void
	{
		$this->joined_with_order_id = $joined_with_order_id;
	}
	
	
	
	
	public function getCurrencyCode(): string
	{
		return $this->currency_code;
	}
	
	public function getCurrency() : Currencies_Currency
	{
		return Currencies::get( $this->currency_code );
	}
	
	public function setCurrencyCode( string $currency_code ): void
	{
		$this->currency_code = $currency_code;
	}
	
	public function getPricelistCode(): string
	{
		return $this->pricelist_code;
	}
	
	public function getPricelist() : Pricelists_Pricelist
	{
		return Pricelists::get( $this->pricelist_code );
	}

	public function setPricelistCode( string $pricelist_code ): void
	{
		$this->pricelist_code = $pricelist_code;
	}

	public function getAvailabilityCode(): string
	{
		return $this->availability_code;
	}
	
	public function getAvailability(): Availabilities_Availability
	{
		return Availabilities::get( $this->availability_code );
	}
	
	public function setAvailabilityCode( string $availability_code ): void
	{
		$this->availability_code = $availability_code;
	}
	
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->getDatePurchased();
	}
	
	public function getNumberSeriesEntityShop(): ?Shops_Shop
	{
		return $this->getShop();
	}
	
	public function getConversionSource(): string
	{
		return $this->conversion_source;
	}
	
	/**
	 * @param Marketing_ConversionSourceDetector_Source[] $conversion_sources
	 * @return void
	 */
	public function setConversionSource( array $conversion_sources ): void
	{
		$conversion_source = [];
		foreach($conversion_sources as $source) {
			$conversion_source[] = $source->getName();
		}
		
		$conversion_source = implode('|', $conversion_source);
		
		$this->conversion_source = $conversion_source;
	}
	
	
	public function getImportSource() : string
	{
		return $this->import_source;
	}

	public function setImportSource( string $import_source ) : void
	{
		$this->import_source = $import_source;
	}

	public function getImportRemoteId() : string
	{
		return $this->import_remote_id;
	}

	public function setImportRemoteId( string $import_remote_id ) : void
	{
		$this->import_remote_id = $import_remote_id;
	}

	public function getIpAddress() : string
	{
		return $this->ip_address;
	}

	public function setIpAddress( string $ip_address ) : void
	{
		$this->ip_address = $ip_address;
	}

	public function getDatePurchased() : Data_DateTime
	{
		return $this->date_purchased;
	}

	public function setDatePurchased( Data_DateTime $date_purchased ) : void
	{
		$this->date_purchased = $date_purchased;
	}

	public function getCustomerId() : int
	{
		return $this->customer_id;
	}

	public function setCustomerId( int $customer_id ) : void
	{
		$this->customer_id = $customer_id;
	}

	public function getEmail() : string
	{
		return $this->email;
	}

	public function setEmail( string $email ) : void
	{
		$this->email = $email;
	}

	public function getPhone() : string
	{
		return $this->phone;
	}

	public function setPhone( string $phone ) : void
	{
		$this->phone = $phone;
	}

	public function getBillingCompanyName() : string
	{
		return $this->billing_company_name;
	}

	public function setBillingCompanyName( string $billing_company_name ) : void
	{
		$this->billing_company_name = $billing_company_name;
	}

	public function getBillingCompanyId() : string
	{
		return $this->billing_company_id;
	}

	public function setBillingCompanyId( string $billing_company_id ) : void
	{
		$this->billing_company_id = $billing_company_id;
	}

	public function getBillingCompanyVatId() : string
	{
		return $this->billing_company_vat_id;
	}

	public function setBillingCompanyVatId( string $billing_company_vat_id ) : void
	{
		$this->billing_company_vat_id = $billing_company_vat_id;
	}

	public function getBillingFirstName() : string
	{
		return $this->billing_first_name;
	}

	public function setBillingFirstName( string $billing_first_name ) : void
	{
		$this->billing_first_name = $billing_first_name;
	}

	public function getBillingSurname(): string
	{
		return $this->billing_surname;
	}

	public function setBillingSurname( string $billing_surname ): void
	{
		$this->billing_surname = $billing_surname;
	}

	public function getBillingAddressStreetNo() : string
	{
		return $this->billing_address_street_no;
	}

	public function setBillingAddressStreetNo( string $billing_address_street_no ) : void
	{
		$this->billing_address_street_no = $billing_address_street_no;
	}

	public function getBillingAddressTown() : string
	{
		return $this->billing_address_town;
	}

	public function setBillingAddressTown( string $billing_address_town ) : void
	{
		$this->billing_address_town = $billing_address_town;
	}

	public function getBillingAddressZip() : string
	{
		return $this->billing_address_zip;
	}

	public function setBillingAddressZip( string $billing_address_zip ) : void
	{
		$this->billing_address_zip = $billing_address_zip;
	}

	public function getBillingAddressCountry() : string
	{
		return $this->billing_address_country;
	}

	public function setBillingAddressCountry( string $billing_address_country ) : void
	{
		$this->billing_address_country = $billing_address_country;
	}

	public function getDeliveryCompanyName(): string
	{
		return $this->delivery_company_name;
	}

	public function setDeliveryCompanyName( string $delivery_company_name ): void
	{
		$this->delivery_company_name = $delivery_company_name;
	}



	public function getDeliveryFirstName() : string
	{
		return $this->delivery_first_name;
	}

	public function setDeliveryFirstName( string $delivery_first_name ) : void
	{
		$this->delivery_first_name = $delivery_first_name;
	}

	/**
	 * @return string
	 */
	public function getDeliverySurname(): string
	{
		return $this->delivery_surname;
	}

	/**
	 * @param string $delivery_surname
	 */
	public function setDeliverySurname( string $delivery_surname ): void
	{
		$this->delivery_surname = $delivery_surname;
	}

	public function getDeliveryAddressStreetNo() : string
	{
		return $this->delivery_address_street_no;
	}

	public function setDeliveryAddressStreetNo( string $delivery_address_street_no ) : void
	{
		$this->delivery_address_street_no = $delivery_address_street_no;
	}

	public function getDeliveryAddressTown() : string
	{
		return $this->delivery_address_town;
	}

	public function setDeliveryAddressTown( string $delivery_address_town ) : void
	{
		$this->delivery_address_town = $delivery_address_town;
	}

	public function getDeliveryAddressZip() : string
	{
		return $this->delivery_address_zip;
	}

	public function setDeliveryAddressZip( string $delivery_address_zip ) : void
	{
		$this->delivery_address_zip = $delivery_address_zip;
	}

	public function getDeliveryAddressCountry() : string
	{
		return $this->delivery_address_country;
	}

	public function setDeliveryAddressCountry( string $delivery_address_country ) : void
	{
		$this->delivery_address_country = $delivery_address_country;
	}

	public function getSpecialRequirements() : string
	{
		return $this->special_requirements;
	}

	public function setSpecialRequirements( string $special_requirements ) : void
	{
		$this->special_requirements = $special_requirements;
	}

	public function getDifferentDeliveryAddress(): bool
	{
		return $this->different_delivery_address;
	}

	public function setDifferentDeliveryAddress( bool $different_delivery_address ) : void
	{
		$this->different_delivery_address = $different_delivery_address;
	}

	public function isCompanyOrder() : bool
	{
		return $this->company_order;
	}

	public function setCompanyOrder( bool $company_order ) : void
	{
		$this->company_order = $company_order;
	}

	public function isNewsletterAccepted() : bool
	{
		return $this->newsletter_accepted;
	}

	public function setNewsletterAccepted( bool $newsletter_accepted ) : void
	{
		$this->newsletter_accepted = $newsletter_accepted;
	}

	public function isSurveyDisagreement() : bool
	{
		return $this->survey_disagreement;
	}

	public function setSurveyDisagreement( bool $survey_disagreement ) : void
	{
		$this->survey_disagreement = $survey_disagreement;
	}

	public function getDeliveryMethodId() : int
	{
		return $this->delivery_method_id;
	}
	
	public function setDeliveryMethod( Delivery_Method_ShopData $delivery_method, string $delivery_point_code ) : void
	{
		/**
		 * @var Order $order
		 */
		$order = $this;
		
		$delivery_order_item = new Order_Item();
		$delivery_order_item->setupDeliveryMethod( $order->getPricelist(), $delivery_method );
		$order->addItem( $delivery_order_item );
		
		$order->setDeliveryMethodId( $delivery_method->getId() );
		if($delivery_method->isPersonalTakeover()) {
			$order->setDeliveryPersonalTakeoverDeliveryPointCode( $delivery_point_code );
			$delivery_order_item->setSubCode( $delivery_point_code );
		}
		
	}

	public function getDeliveryMethod() : Delivery_Method_ShopData
	{
		return Delivery_Method_ShopData::get( $this->getDeliveryMethodId(), $this->getShop() );
	}

	public function setDeliveryMethodId( int $delivery_method_id ) : void
	{
		$this->delivery_method_id = $delivery_method_id;
	}

	public function getDeliveryPersonalTakeoverDeliveryPointCode() : string
	{
		return $this->delivery_personal_takeover_delivery_point_code;
	}

	public function setDeliveryPersonalTakeoverDeliveryPointCode( string $delivery_personal_takeover_delivery_point_code ) : void
	{
		$this->delivery_personal_takeover_delivery_point_code = $delivery_personal_takeover_delivery_point_code;
	}
	
	
	
	public function setPaymentMethod( Payment_Method_ShopData $payment_method, string $payment_option_id='' ) : void
	{
		/**
		 * @var Order $order
		 */
		$order = $this;
		
		$payment_order_item = new Order_Item();
		$payment_order_item->setupPaymentMethod( $order->getPricelist(), $payment_method );
		$order->addItem( $payment_order_item );
		
		$order->setPaymentMethodId( $payment_method->getId() );
		$order->setPaymentRequired(
			!$payment_method->getKind()->isCOD()
		);
		
		if($payment_option_id) {
			$payment_option = $payment_method->getOptions()[$payment_option_id]??null;
			if($payment_option) {
				$order->setPaymentMethodSpecification( $payment_option->getId() );
				$payment_order_item->setSubCode($payment_option->getId());
				$payment_order_item->setDescription( $payment_option->getTitle() );
			}
		}
		
		
	}
	

	public function getPaymentMethodId() : string
	{
		return $this->payment_method_id;
	}

	public function setPaymentMethodId( string $payment_method_id ) : void
	{
		$this->payment_method_id = $payment_method_id;
	}

	public function getPaymentMethod() : Payment_Method_ShopData
	{
		return Payment_Method_ShopData::get( $this->getPaymentMethodId(), $this->getShop() );
	}

	public function getPaymentMethodSpecification() : string
	{
		return $this->payment_method_specification;
	}

	public function setPaymentMethodSpecification( int $payment_method_specification ) : void
	{
		$this->payment_method_specification = $payment_method_specification;
	}
	
	public function getDiscountAmount() : float
	{
		return $this->discount_amount;
	}

	public function getTotalAmount() : float
	{
		return $this->total_amount;
	}

	public function getProductAmount() : float
	{
		return $this->product_amount;
	}

	public function getDeliveryAmount() : float
	{
		return $this->delivery_amount;
	}


	public function getPaymentAmount() : float
	{
		return $this->payment_amount;
	}

	public function getServiceAmount() : float
	{
		return $this->service_amount;
	}

	/**
	 * @return Order_Item[]
	 */
	public function getItems() : iterable
	{
		return $this->items;
	}

	public function addItem( Order_Item $item ) : void
	{
		$this->items[] = $item;
	}

	
	
	/**
	 * @return Order_Event[]
	 */
	public function getHistory() : array
	{
		return Order_Event::getForOrder( $this->getId() );
	}
	
	public function getWarehouseId(): int
	{
		return $this->warehouse_id;
	}
	
	public function setWarehouseId( int $warehouse_id ): void
	{
		$this->warehouse_id = $warehouse_id;
	}
	
	public function getWarehouse() : ?WarehouseManagement_Warehouse
	{
		if(!$this->warehouse) {
			$this->warehouse = WarehouseManagement_Warehouse::get( $this->warehouse_id );
		}
		
		return $this->warehouse;
	}
	
	public function setWarehouse( WarehouseManagement_Warehouse $warehouse ) : void
	{
		$this->warehouse = $warehouse;
		$this->setWarehouseId( $warehouse->getId() );
		static::updateData(
			data: [
				'warehouse_id' => $this->warehouse_id,
			],
			where: [
				'id' => $this->id
			]
		);
	}
	

	public function recalculate() : void
	{

		$this->total_amount = 0.0;
		$this->product_amount = 0.0;
		$this->service_amount = 0.0;
		$this->delivery_amount = 0.0;
		$this->payment_amount = 0.0;
		$this->discount_amount = 0.0;
		
		$this->total_volume_of_products = 0.0;
		$this->total_weight_of_products = 0.0;

		foreach( $this->items as $order_item ) {
			if ( $order_item->isPhysicalProduct() ) {
				$product = Product_ShopData::get( $order_item->getItemId(), $this->getShop() );
				
				$this->total_volume_of_products += ($product->getBoxesVolume()*$order_item->getNumberOfUnits());
				$this->total_weight_of_products += ($product->getBoxesWeight()*$order_item->getNumberOfUnits());
			}
			
			

			$amount = $order_item->getTotalAmount();

			$this->total_amount += $amount;

			switch($order_item->getType()) {
				case Order_Item::ITEM_TYPE_GIFT:
				break;
				case Order_Item::ITEM_TYPE_PRODUCT:
				case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
					$this->product_amount += $amount;
				break;
				case Order_Item::ITEM_TYPE_SERVICE:
					$this->service_amount += $amount;
				break;
				case Order_Item::ITEM_TYPE_PAYMENT:
					$this->payment_amount += $amount;
				break;
				case Order_Item::ITEM_TYPE_DELIVERY:
					$this->delivery_amount += $amount;
				break;
				case Order_Item::ITEM_TYPE_DISCOUNT:
					$this->discount_amount += $amount;
					break;

			}
		}
		
		
	}

	protected function generateKey() : void
	{
		$this->key = md5( time().uniqid().uniqid() );
	}

	public function getKey() : string
	{
		return $this->key;
	}

	public function beforeSave(): void
	{
		parent::beforeSave();
		if($this->getIsNew()) {
			$this->generateKey();
		}
	}
	
	public function afterAdd(): void
	{
		parent::afterAdd();
		$this->generateNumber();
	}
	
	
	public static function get( int $id ) : static|null
	{
		return static::load( $id );
	}

	public static function getByKey( string $key ) : Order|null
	{
		$orders = Order::fetch(['order' => [
			'key' => $key
		]]);

		if(count($orders)!=1) {
			return null;
		}

		return $orders[0];
	}
	
	public static function getByImportSource( string $import_source, string $import_remote_id, Shops_Shop $shop ) : Order|null
	{
		$where = $shop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'import_source' => $import_source,
			'AND',
			'import_remote_id' => $import_remote_id
		];
		
		$orders = Order::fetch(['' => $where]);
		
		if(count($orders)!=1) {
			return null;
		}
		
		return $orders[0];
	}
	
	
	public static function getByNumber( string $number, Shops_Shop $shop ) : Order|null
	{
		$where = $shop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'number' => $number
		];
		
		$orders = Order::fetch(['' => $where]);
		
		if(count($orders)!=1) {
			return null;
		}
		
		return $orders[0];
	}
	
	/**
	 * @return Order[]
	 */
	public static function getList() : iterable
	{
		$where = [];

		return static::fetchInstances( $where );
	}
	
	
	/**
	 * @return static[]
	 */
	public static function getOrdersWaitingForGoods() : array
	{
		$where = [
			'cancelled' => false,
			'AND',
			'dispatched' => false,
			'AND',
			'all_items_available' => false
		];
		
		return Order::fetch(['' => $where], order_by: 'id' );
	}
	
	
	public function setBillingAddress( Customer_Address $address ) : void
	{
		$this->setBillingCompanyName( $address->getCompanyName() );
		$this->setBillingCompanyId( $address->getCompanyId() );
		$this->setBillingCompanyVatId( $address->getCompanyVatId() );
		$this->setBillingFirstName( $address->getFirstName() );
		$this->setBillingSurname( $address->getSurname() );
		$this->setBillingAddressStreetNo( $address->getAddressStreetNo() );
		$this->setBillingAddressTown( $address->getAddressTown() );
		$this->setBillingAddressZip( $address->getAddressZip() );
		$this->setBillingAddressCountry( $address->getAddressCountry() );
	}
	
	public function getBillingAddress() : Customer_Address
	{
		$address = new Customer_Address();
		
		$address->setCompanyName( $this->getBillingCompanyName( ) );
		$address->setCompanyId( $this->getBillingCompanyId( ) );
		$address->setCompanyVatId( $this->getBillingCompanyVatId( ) );
		$address->setFirstName( $this->getBillingFirstName( ) );
		$address->setSurname( $this->getBillingSurname( ) );
		$address->setAddressStreetNo( $this->getBillingAddressStreetNo( ) );
		$address->setAddressTown( $this->getBillingAddressTown( ) );
		$address->setAddressZip( $this->getBillingAddressZip( ) );
		$address->setAddressCountry( $this->getBillingAddressCountry( ) );
		
		return $address;
	}
	
	
	
	public function setDeliveryAddress( Customer_Address $address ) : void
	{
		$this->setDeliveryCompanyName( $address->getCompanyName() );
		$this->setDeliveryFirstName( $address->getFirstName() );
		$this->setDeliverySurname( $address->getSurname() );
		$this->setDeliveryAddressStreetNo( $address->getAddressStreetNo() );
		$this->setDeliveryAddressTown( $address->getAddressTown() );
		$this->setDeliveryAddressZip( $address->getAddressZip() );
		$this->setDeliveryAddressCountry( $address->getAddressCountry() );
	}
	
	
	public function getDeliveryAddress() : Customer_Address
	{
		$address = new Customer_Address();
		
		$address->setCompanyName( $this->getDeliveryCompanyName( ) );
		$address->setFirstName( $this->getDeliveryFirstName( ) );
		$address->setSurname( $this->getDeliverySurname( ) );
		$address->setAddressStreetNo( $this->getDeliveryAddressStreetNo( ) );
		$address->setAddressTown( $this->getDeliveryAddressTown( ) );
		$address->setAddressZip( $this->getDeliveryAddressZip( ) );
		$address->setAddressCountry( $this->getDeliveryAddressCountry( ) );
		
		return $address;
	}
	
	public function getAdminTitle(): string
	{
		return $this->getNumber();
	}
	
	public function getTotalWeightOfProducts(): float
	{
		return $this->total_weight_of_products;
	}
	
	public function setTotalWeightOfProducts( float $total_weight_of_products ): void
	{
		$this->total_weight_of_products = $total_weight_of_products;
	}
	
	public function getTotalVolumeOfProducts(): float
	{
		return $this->total_volume_of_products;
	}
	
	public function setTotalVolumeOfProducts( float $total_volume_of_products ): void
	{
		$this->total_volume_of_products = $total_volume_of_products;
	}
	
	
	/**
	 * @return Order_ProductOverviewItem[]
	 */
	public function getPhysicalProductOverview() : array
	{
		/**
		 * @var Order_ProductOverviewItem[] $result
		 */
		$result = [];
		foreach($this->getItems() as $item) {
			if(!$item->isPhysicalProduct()) {
				continue;
			}
			
			if( ($set_items=$item->getSetItems()) ) {
				foreach( $item->getSetItems() as $set_item ) {
					$product = Product_ShopData::get( $set_item->getItemId(), $this->getShop() );
					
					if(
						!$product ||
						!$product->isPhysicalProduct()
					) {
						continue;
					}
					
					$number_of_units = $set_item->getNumberOfUnits()*$item->getNumberOfUnits();
					
					$id = $product->getId();
					if(!isset($result[$id])) {
						$result[$id] = new Order_ProductOverviewItem( $product, $number_of_units );
					} else {
						$result[$id]->addNumberOfUnits( $number_of_units );
					}
				}
				
				continue;
			}
			
			$product = Product_ShopData::get( $item->getItemId(), $this->getShop() );
			if(
				!$product ||
				!$product->isPhysicalProduct()
			) {
				continue;
			}
			
			$number_of_units = $item->getNumberOfUnits();
			
			$id = $product->getId();
			
			if(!isset($result[$id])) {
				$result[$id] = new Order_ProductOverviewItem( $product, $number_of_units );
			} else {
				$result[$id]->addNumberOfUnits( $number_of_units );
			}
		}
		
		return $result;
		
	}
	
	public function getHasPhysicalProducts() : bool
	{
		return count($this->getPhysicalProductOverview())>0;
	}
	
	
	/**
	 * @return Order_ProductOverviewItem[]
	 */
	public function getVirtualProductOverview() : array
	{
		/**
		 * @var Order_ProductOverviewItem[] $result
		 */
		$result = [];
		foreach($this->getItems() as $item) {
			if($item->isPhysicalProduct()) {
				continue;
			}
			
			if( ($set_items=$item->getSetItems()) ) {
				foreach( $item->getSetItems() as $set_item ) {
					$product = Product_ShopData::get( $set_item->getItemId(), $this->getShop() );
					
					if(
						!$product ||
						!$product->isVirtual()
					) {
						continue;
					}
					
					$number_of_units = $set_item->getNumberOfUnits()*$item->getNumberOfUnits();
					
					$id = $product->getId();
					if(!isset($result[$id])) {
						$result[$id] = new Order_ProductOverviewItem( $product, $number_of_units );
					} else {
						$result[$id]->addNumberOfUnits( $number_of_units );
					}
				}
				
				continue;
			}
			
			$product = Product_ShopData::get( $item->getItemId(), $this->getShop() );
			if(
				!$product ||
				!$product->isVirtual()
			) {
				continue;
			}
			
			$number_of_units = $item->getNumberOfUnits();
			
			$id = $product->getId();
			
			if(!isset($result[$id])) {
				$result[$id] = new Order_ProductOverviewItem( $product, $number_of_units );
			} else {
				$result[$id]->addNumberOfUnits( $number_of_units );
			}
		}
		
		return $result;
		
	}
	
	
	public function getHasVirtualProducts() : bool
	{
		return count($this->getVirtualProductOverview())>0;
	}
	
	public function getPromisedDeliveryDate(): ?Data_DateTime
	{
		return $this->promised_delivery_date;
	}
	
	public function setPromisedDeliveryDate( Data_DateTime|null|string $date ): void
	{
		$this->promised_delivery_date = Data_DateTime::catchDate( $date );
	}
	
	
	public function basicClone() : Order
	{
		$new_order = new Order();
		
		$new_order->setDatePurchased( Data_DateTime::now() );
		$new_order->setIpAddress('clone:'.$this->getNumber());
		
		$new_order->setShop( $this->getShop() );
		$new_order->setCurrencyCode( $this->getCurrencyCode() );
		$new_order->setAvailabilityCode( $this->getAvailabilityCode() );
		$new_order->setPricelistCode( $this->getPricelistCode() );
		
		$new_order->setCustomerId( $this->getCustomerId() );
		$new_order->setPhone( $this->getPhone() );
		$new_order->setEmail( $this->getEmail() );
		
		$new_order->setBillingAddress( $this->getBillingAddress());
		$new_order->setDeliveryAddress( $this->getDeliveryAddress());
		
		$new_order->setDifferentDeliveryAddress( $this->getDifferentDeliveryAddress() );
		$new_order->setSpecialRequirements( $this->getSpecialRequirements() );
		$new_order->setCompanyOrder( $this->isCompanyOrder() );
		
		$new_order->setDeliveryMethod( $this->getDeliveryMethod(), $this->getDeliveryPersonalTakeoverDeliveryPointCode() );
		$new_order->setPaymentMethod( $this->getPaymentMethod(), $this->getPaymentMethodSpecification() );
		
		return $new_order;
	}
	
}
