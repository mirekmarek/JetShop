<?php
namespace JetShop;

use Jet\DataModel;
use Jet\Data_DateTime;
use Jet\DataModel_Definition;

trait Core_Order_Traits_DataModel {

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
	protected string $shop_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $import_source = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $import_remote_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
		form_field_type: false
	)]
	protected string $ip_address = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_purchased = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
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
	protected string $billing_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_address = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_town = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_zip = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $billing_country = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_name = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_address = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_town = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_zip = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $delivery_country = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	protected string $customer_notes = '';

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
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $delivery_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $delivery_specification = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $payment_method_id = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $payment_method_specification = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_price_without_discount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $product_price_without_discount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $product_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $delivery_price_without_discount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $delivery_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $payment_price_without_discount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $payment_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $service_price_without_discount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $service_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $discount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $discount_percentage = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $state = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $status_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $all_products_in_stocks = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Order_Item::class
	)]
	protected $items;

	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getShopId() : string
	{
		return $this->shop_id;
	}

	public function setShopId( string $shop_id ) : void
	{
		$this->shop_id = $shop_id;
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

	public function getBillingName() : string
	{
		return $this->billing_name;
	}

	public function setBillingName( string $billing_name ) : void
	{
		$this->billing_name = $billing_name;
	}

	public function getBillingAddress() : string
	{
		return $this->billing_address;
	}

	public function setBillingAddress( string $billing_address ) : void
	{
		$this->billing_address = $billing_address;
	}

	public function getBillingTown() : string
	{
		return $this->billing_town;
	}

	public function setBillingTown( string $billing_town ) : void
	{
		$this->billing_town = $billing_town;
	}

	public function getBillingZip() : string
	{
		return $this->billing_zip;
	}

	public function setBillingZip( string $billing_zip ) : void
	{
		$this->billing_zip = $billing_zip;
	}

	public function getBillingCountry() : string
	{
		return $this->billing_country;
	}

	public function setBillingCountry( string $billing_country ) : void
	{
		$this->billing_country = $billing_country;
	}

	public function getDeliveryName() : string
	{
		return $this->delivery_name;
	}

	public function setDeliveryName( string $delivery_name ) : void
	{
		$this->delivery_name = $delivery_name;
	}

	public function getDeliveryAddress() : string
	{
		return $this->delivery_address;
	}

	public function setDeliveryAddress( string $delivery_address ) : void
	{
		$this->delivery_address = $delivery_address;
	}

	public function getDeliveryTown() : string
	{
		return $this->delivery_town;
	}

	public function setDeliveryTown( string $delivery_town ) : void
	{
		$this->delivery_town = $delivery_town;
	}

	public function getDeliveryZip() : string
	{
		return $this->delivery_zip;
	}

	public function setDeliveryZip( string $delivery_zip ) : void
	{
		$this->delivery_zip = $delivery_zip;
	}

	public function getDeliveryCountry() : string
	{
		return $this->delivery_country;
	}

	public function setDeliveryCountry( string $delivery_country ) : void
	{
		$this->delivery_country = $delivery_country;
	}

	public function getCustomerNotes() : string
	{
		return $this->customer_notes;
	}

	public function setCustomerNotes( string $customer_notes ) : void
	{
		$this->customer_notes = $customer_notes;
	}

	public function isDifferentDeliveryAddress(): bool
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

	public function getDeliveryId() : string
	{
		return $this->delivery_id;
	}

	public function setDeliveryId( string $delivery_id ) : void
	{
		$this->delivery_id = $delivery_id;
	}

	public function getDeliverySpecification() : string
	{
		return $this->delivery_specification;
	}

	public function setDeliverySpecification( string $delivery_specification ) : void
	{
		$this->delivery_specification = $delivery_specification;
	}

	public function getPaymentMethodId() : string
	{
		return $this->payment_method_id;
	}

	public function setPaymentMethodId( string $payment_method_id ) : void
	{
		$this->payment_method_id = $payment_method_id;
	}

	public function getPaymentMethodSpecification() : string
	{
		return $this->payment_method_specification;
	}

	public function setPaymentMethodSpecification( string $payment_method_specification ) : void
	{
		$this->payment_method_specification = $payment_method_specification;
	}

	public function getTotalPriceWithoutDiscount() : float
	{
		return $this->total_price_without_discount;
	}

	public function getDiscount() : float
	{
		return $this->discount;
	}

	public function getTotalPrice() : float
	{
		return $this->total_price;
	}

	public function getProductPriceWithoutDiscount() : float
	{
		return $this->product_price_without_discount;
	}

	public function getProductPrice() : float
	{
		return $this->product_price;
	}

	public function getDeliveryPriceWithoutDiscount() : float
	{
		return $this->delivery_price_without_discount;
	}

	public function getDeliveryPrice() : float
	{
		return $this->delivery_price;
	}

	public function getPaymentPriceWithoutDiscount() : float
	{
		return $this->payment_price_without_discount;
	}

	public function getPaymentPrice() : float
	{
		return $this->payment_price;
	}

	public function getServicePriceWithoutDiscount() : float
	{
		return $this->service_price_without_discount;
	}

	public function getServicePrice() : float
	{
		return $this->service_price;
	}

	public function getDiscountPercentage() : float
	{
		return $this->discount_percentage;
	}

	/**
	 * @return Order_Item[]
	 */
	public function getItems() : array
	{
		return $this->items;
	}

	public function getState() : string
	{
		return $this->state;
	}

	public function setState( string $state ) : void
	{
		$this->state = $state;
	}

	public function getStatusId() : int
	{
		return $this->status_id;
	}

	public function setStatusId( int $status_id ) : void
	{
		$this->status_id = $status_id;
	}

	public function getAllProductsInStocks() : bool
	{
		return $this->all_products_in_stocks;
	}
}