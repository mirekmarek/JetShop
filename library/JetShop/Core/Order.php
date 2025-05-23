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
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_Order;
use JetApplication\Availabilities;
use JetApplication\Availability;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Currencies;
use JetApplication\Currency;
use JetApplication\Customer_Address;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\EShopEntity_Definition;
use JetApplication\Marketing_ConversionSourceDetector_Source;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\Order_Item;
use JetApplication\Order_ProductOverviewItem;
use JetApplication\Order_Trait_Events;
use JetApplication\Order_Trait_Status;
use JetApplication\Order_Trait_Changes;
use JetApplication\Payment_Method;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\Pricelists;
use JetApplication\Pricelist;
use JetApplication\Product_EShopData;
use JetApplication\EShop;
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
#[EShopEntity_Definition(
	entity_name_readable: 'Order',
	admin_manager_interface: Admin_Managers_Order::class
)]
abstract class Core_Order extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasNumberSeries_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_Admin_Trait;
	use EShopEntity_HasStatus_Trait;
	use Core_EShopEntity_HasEvents_Trait;
	
	
	use Context_ProvidesContext_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	
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
	protected float $product_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $product_amount_without_VAT = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $delivery_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $delivery_amount_without_VAT = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $payment_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $payment_amount_without_VAT = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $service_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $service_amount_without_VAT = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $discount_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $discount_amount_without_VAT = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount_with_VAT = 0.0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_amount_without_VAT = 0.0;
	
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
	
	public function getCurrency() : Currency
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
	
	public function getPricelist() : Pricelist
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
	
	public function getAvailability(): Availability
	{
		return Availabilities::get( $this->availability_code );
	}
	
	public function setAvailabilityCode( string $availability_code ): void
	{
		$this->availability_code = $availability_code;
	}
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return true;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Orders';
	}
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->getDatePurchased();
	}
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return $this->getEshop();
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
	
	public function setDeliveryMethod( Delivery_Method $delivery_method, string $delivery_point_code ) : void
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

	public function getDeliveryMethod() : ?Delivery_Method
	{
		return Delivery_Method::get( $this->getDeliveryMethodId() );
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
	
	
	
	public function setPaymentMethod( Payment_Method $payment_method, string $payment_option_code='' ) : void
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
		
		if($payment_option_code) {
			$payment_option = $payment_method->getOptions()[$payment_option_code]??null;
			if($payment_option) {
				$order->setPaymentMethodSpecification( $payment_option->getInternalCode() );
				$payment_order_item->setSubCode($payment_option->getInternalCode());
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

	public function getPaymentMethod() : ?Payment_Method
	{
		return Payment_Method::get( $this->getPaymentMethodId() );
	}

	public function getPaymentMethodSpecification() : string
	{
		return $this->payment_method_specification;
	}

	public function setPaymentMethodSpecification( string $payment_method_specification ) : void
	{
		$this->payment_method_specification = $payment_method_specification;
	}
	
	public function getDiscountAmount_WithVAT() : float
	{
		return $this->discount_amount_with_VAT;
	}
	
	public function getDiscountAmount_WithoutVAT() : float
	{
		return $this->discount_amount_without_VAT;
	}

	public function getTotalAmount_WithVAT() : float
	{
		return $this->total_amount_with_VAT;
	}
	
	public function getTotalAmount_WithoutVAT() : float
	{
		return $this->total_amount_without_VAT;
	}

	public function getProductAmount_WithVAT() : float
	{
		return $this->product_amount_with_VAT;
	}
	
	public function getProductAmount_WithoutVAT() : float
	{
		return $this->product_amount_without_VAT;
	}

	public function getDeliveryAmount_WithVAT() : float
	{
		return $this->delivery_amount_with_VAT;
	}
	
	public function getDeliveryAmount_WithoutVAT() : float
	{
		return $this->delivery_amount_without_VAT;
	}


	public function getPaymentAmount_WithVAT() : float
	{
		return $this->payment_amount_with_VAT;
	}
	
	public function getPaymentAmount_WithoutVAT() : float
	{
		return $this->payment_amount_without_VAT;
	}

	public function getServiceAmount_WithVAT() : float
	{
		return $this->service_amount_with_VAT;
	}
	
	public function getServiceAmount_WithoutVAT() : float
	{
		return $this->service_amount_without_VAT;
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
		return Order_Event::getEventsList( $this->getId() );
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

		$this->total_amount_with_VAT = 0.0;
		$this->total_amount_without_VAT = 0.0;
		$this->product_amount_with_VAT = 0.0;
		$this->product_amount_without_VAT = 0.0;
		$this->service_amount_with_VAT = 0.0;
		$this->service_amount_without_VAT = 0.0;
		$this->delivery_amount_with_VAT = 0.0;
		$this->delivery_amount_without_VAT = 0.0;
		$this->payment_amount_with_VAT = 0.0;
		$this->payment_amount_without_VAT = 0.0;
		$this->discount_amount_with_VAT = 0.0;
		$this->discount_amount_without_VAT = 0.0;
		
		$this->total_volume_of_products = 0.0;
		$this->total_weight_of_products = 0.0;

		foreach( $this->items as $order_item ) {
			if ( $order_item->isPhysicalProduct() ) {
				$product = Product_EShopData::get( $order_item->getItemId(), $this->getEshop() );
				if($product) {
					$this->total_volume_of_products += ($product->getBoxesVolume()*$order_item->getNumberOfUnits());
					$this->total_weight_of_products += ($product->getBoxesWeight()*$order_item->getNumberOfUnits());
				}
			}
			
			

			$amount_with_VAT = $order_item->getTotalAmount_WithVat();
			$amount_without_VAT = $order_item->getTotalAmount_WithoutVat();

			$this->total_amount_with_VAT += $amount_with_VAT;
			$this->total_amount_without_VAT += $amount_without_VAT;

			switch($order_item->getType()) {
				case Order_Item::ITEM_TYPE_GIFT:
				break;
				case Order_Item::ITEM_TYPE_PRODUCT:
				case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
					$this->product_amount_with_VAT += $amount_with_VAT;
					$this->product_amount_without_VAT += $amount_without_VAT;
				break;
				case Order_Item::ITEM_TYPE_SERVICE:
					$this->service_amount_with_VAT += $amount_with_VAT;
					$this->service_amount_without_VAT += $amount_without_VAT;
				break;
				case Order_Item::ITEM_TYPE_PAYMENT:
					$this->payment_amount_with_VAT += $amount_with_VAT;
					$this->payment_amount_without_VAT += $amount_without_VAT;
				break;
				case Order_Item::ITEM_TYPE_DELIVERY:
					$this->delivery_amount_with_VAT += $amount_with_VAT;
					$this->delivery_amount_without_VAT += $amount_without_VAT;
				break;
				case Order_Item::ITEM_TYPE_DISCOUNT:
					$this->discount_amount_with_VAT += $amount_with_VAT;
					$this->discount_amount_without_VAT += $amount_without_VAT;
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
	
	public static function getByImportSource( string $import_source, string $import_remote_id, EShop $eshop ) : Order|null
	{
		$where = $eshop->getWhere();
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
	
	
	public static function getByNumber( string $number, EShop $eshop ) : Order|null
	{
		$where = $eshop->getWhere();
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
					$product = Product_EShopData::get( $set_item->getItemId(), $this->getEshop() );
					
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
			
			$product = Product_EShopData::get( $item->getItemId(), $this->getEshop() );
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
					$product = Product_EShopData::get( $set_item->getItemId(), $this->getEshop() );
					
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
			
			$product = Product_EShopData::get( $item->getItemId(), $this->getEshop() );
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
		
		$new_order->setEshop( $this->getEshop() );
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
	
	public function isEditable(): bool
	{
		
		if(
			$this->cancelled ||
			$this->delivered ||
			$this->dispatch_started
		) {
			return false;
		}
		
		
		return true;
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
	
}
