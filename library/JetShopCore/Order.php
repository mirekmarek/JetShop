<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;

#[DataModel_Definition(
	name: 'order',
	database_table_name: 'orders',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: [
		'id_property_name' => 'id'
	],
	key: [
		'name' => 'key',
		'property_names' => [
			'key'
		],
		'type' => DataModel::KEY_TYPE_UNIQUE
	]
)]
abstract class Core_Order extends DataModel {
	use CommonEntity_ShopRelationTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
		form_field_type: false
	)]
	protected int $id = 0;

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		form_field_type: false
	)]
	protected string $key = '';

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
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $delivery_method_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $delivery_personal_takeover_place_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $payment_method_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	protected string $payment_method_specification = '';

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
	protected float $total_price_without_discount = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_FLOAT
	)]
	protected float $total_price = 0.0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true
	)]
	protected string $status_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true
	)]
	protected bool $all_items_available = false;

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

	public function getDeliveryMethodCode() : string
	{
		return $this->delivery_method_code;
	}

	public function getDeliveryMethod() : Delivery_Method
	{
		return Delivery_Method::get( $this->getDeliveryMethodCode() );
	}

	public function setDeliveryMethodCode( string $delivery_method_code ) : void
	{
		$this->delivery_method_code = $delivery_method_code;
	}

	public function getDeliveryPersonalTakeoverPlaceCode() : string
	{
		return $this->delivery_personal_takeover_place_code;
	}

	public function setDeliveryPersonalTakeoverPlaceCode( string $delivery_personal_takeover_place_code ) : void
	{
		$this->delivery_personal_takeover_place_code = $delivery_personal_takeover_place_code;
	}

	public function getPaymentMethodCode() : string
	{
		return $this->payment_method_code;
	}

	public function setPaymentMethodCode( string $payment_method_code ) : void
	{
		$this->payment_method_code = $payment_method_code;
	}

	public function getPaymentMethod() : Payment_Method
	{
		return Payment_Method::get( $this->getPaymentMethodCode() );
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
	public function getItems() : iterable
	{
		return $this->items;
	}

	public function addItem( Order_Item $item ) : void
	{
		$this->items[] = $item;
	}

	public function getStatusCode() : string
	{
		return $this->status_code;
	}

	public function setStatusCode( string $status_code ) : void
	{
		$this->status_code = $status_code;
		//TODO: history
	}

	public function getAllItemsAvailable() : bool
	{
		return $this->all_items_available;
	}

	public function setAllItemsAvailable( bool $all_items_available ): void
	{
		$this->all_items_available = $all_items_available;
	}





	public function recalculate() : void
	{

		$this->total_price = 0.0;
		$this->product_price = 0.0;
		$this->service_price = 0.0;
		$this->delivery_price = 0.0;
		$this->payment_price = 0.0;

		$this->all_items_available = true;

		foreach( $this->items as $discount_item ) {
			if(
				(
					$discount_item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
					$discount_item->getType()==Order_Item::ITEM_TYPE_GIFT
				)
				&&
				!$discount_item->isAvailable()
			) {
				$this->all_items_available = false;
			}

			if($discount_item->getType()==Order_Item::ITEM_TYPE_DISCOUNT) {
				continue;
			}

			$price = $discount_item->getTotalAmount();

			$this->total_price += $price;

			switch($discount_item->getType()) {
				case Order_Item::ITEM_TYPE_GIFT:
				break;
				case Order_Item::ITEM_TYPE_PRODUCT:
				case Order_Item::ITEM_TYPE_VIRTUAL_PRODUCT:
					$this->product_price += $price;
				break;
				case Order_Item::ITEM_TYPE_SERVICE:
					$this->service_price += $price;
				break;
				case Order_Item::ITEM_TYPE_PAYMENT:
					$this->payment_price += $price;
				break;
				case Order_Item::ITEM_TYPE_DELIVERY:
					$this->delivery_price += $price;
				break;

			}

		}

		$this->total_price_without_discount = $this->total_price;
		$this->product_price_without_discount = $this->product_price;
		$this->service_price_without_discount = $this->service_price;
		$this->delivery_price_without_discount = $this->delivery_price;
		$this->payment_price_without_discount = $this->payment_price;

		$this->recalculate_discounts();
	}

	protected function recalculate_discounts() : void
	{
		$this->discount = 0.0;
		$this->discount_percentage = 0.0;

		$applyNominalDiscount = function( Order_Item $discount_item, float &$price  ) {
			$discount = $discount_item->getItemAmount();

			if($discount>$price) {
				$discount = $price;
			}

			if($discount>$this->total_price) {
				$discount = $this->total_price;
			}

			$price -= $discount;

			$discount_item->setTotalAmount( $discount );

			$this->discount += $discount;
			$this->total_price -= $discount;
		};

		$applyPrcDiscount = function( Order_Item $discount_item, float &$price, float $orig_price  ) {
			$discount = $discount_item->getItemAmount();
			$discount = Price::round($orig_price * ($discount/100), $this->getShop() );

			if($discount>$price) {
				$discount = $price;
			}

			if($discount>$this->total_price) {
				$discount = $this->total_price;
			}

			$price -= $discount;

			$discount_item->setTotalAmount( $discount );

			$this->discount += $discount;
			$this->total_price -= $discount;
		};


		foreach( $this->items as $discount_item ) {
			if($discount_item->getType()!=Order_Item::ITEM_TYPE_DISCOUNT) {
				continue;
			}

			switch($discount_item->getSubType()) {
				case Order_Item::DISCOUNT_TYPE_PRODUCTS_PERCENT:
					$applyPrcDiscount(
						$discount_item,
						$this->product_price,
						$this->product_price_without_discount
					);
					break;
				case Order_Item::DISCOUNT_TYPE_PRODUCTS_AMOUNT:
					$applyNominalDiscount(
						$discount_item,
						$this->product_price
					);
					break;
				case Order_Item::DISCOUNT_TYPE_SERVICE_PERCENT:
					$applyPrcDiscount(
						$discount_item,
						$this->service_price,
						$this->service_price_without_discount
					);
					break;
				case Order_Item::DISCOUNT_TYPE_SERVICE_AMOUNT:
					$applyNominalDiscount(
						$discount_item,
						$this->service_price
					);
					break;
				case Order_Item::DISCOUNT_TYPE_DELIVERY_PERCENT:
					$applyPrcDiscount(
						$discount_item,
						$this->delivery_price,
						$this->delivery_price_without_discount
					);
					break;
				case Order_Item::DISCOUNT_TYPE_DELIVERY_AMOUNT:
					$applyNominalDiscount(
						$discount_item,
						$this->delivery_price
					);
					break;

				case Order_Item::DISCOUNT_TYPE_PAYMENT_PERCENT:
					$applyPrcDiscount(
						$discount_item,
						$this->payment_price,
						$this->payment_price_without_discount
					);
					break;
				case Order_Item::DISCOUNT_TYPE_PAYMENT_AMOUNT:
					$applyNominalDiscount(
						$discount_item,
						$this->payment_price
					);
					break;
				case Order_Item::DISCOUNT_TYPE_TOTAL_AMOUNT:
				case Order_Item::DISCOUNT_TYPE_TOTAL_PERCENT:
					continue 2;
			}
		}


		foreach( $this->items as $discount_item ) {
			if($discount_item->getType()!=Order_Item::ITEM_TYPE_DISCOUNT) {
				continue;
			}

			switch($discount_item->getSubType()) {
				case Order_Item::DISCOUNT_TYPE_TOTAL_AMOUNT:
					$applyNominalDiscount(
						$discount_item,
						$this->total_price
					);
					break;
				case Order_Item::DISCOUNT_TYPE_TOTAL_PERCENT:
					$applyPrcDiscount(
						$discount_item,
						$this->total_price,
						$this->total_price_without_discount
					);
					break;
				default:
					continue 2;
			}
		}


		if($this->discount>0) {
			$this->discount_percentage = (1-$this->total_price / $this->total_price_without_discount)*100;
			$this->discount_percentage = round($this->discount_percentage, 3);
		}

	}

	/**
	 * @return string
	 */
	public function getKey() : string
	{
		return $this->key;
	}

	public function beforeSave(): void
	{
		if($this->getIsNew()) {
			$this->key = md5( time().uniqid().uniqid() );
		}
	}


	public static function get( int $id ) : static|null
	{
		return Order::load( $id );
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

	/**
	 * @return iterable
	 */
	public static function getList() : iterable
	{
		$where = [];

		return static::fetchInstances( $where );
	}


	public function onNewOrderSave() : void
	{
		/**
		 * @var Order $this
		 */
		foreach(Discounts::getActiveModules() as $dm) {
			$dm->Order_saved( $this );
		}

		$this->event( 'NewOrderSave' )->handleImmediately();

	}

	public function event( string $event ) : Order_Event
	{
		$e = Order_Event::newEvent( $this->getId(), $event );

		return $e;
	}
}