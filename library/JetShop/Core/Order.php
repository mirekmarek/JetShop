<?php
namespace JetShop;

use Jet\Data_DateTime;
use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\Customer_Address;
use JetApplication\Delivery_Method_ShopData;
use JetApplication\Discounts;
use JetApplication\Entity_WithShopRelation;
use JetApplication\Order_Item;
use JetApplication\Order_Status_History;
use JetApplication\Payment_Method_ShopData;
use JetApplication\Order;
use JetApplication\Order_Event;

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
abstract class Core_Order extends Entity_WithShopRelation {
	
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
	protected string $number = '';
	
	
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
	protected string $delivery_personal_takeover_place_code = '';

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
	)]
	protected int $status_id = 0;

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
	
	/**
	 * @var Order_Status_History[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Order_Status_History::class
	)]
	protected array $status_history = [];


	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getNumber() : string
	{
		return $this->number;
	}
	
	public function generateNumber() : void
	{
		$this->number = $this->id;
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

	public function getDeliveryMethodId() : int
	{
		return $this->delivery_method_id;
	}

	public function getDeliveryMethod() : Delivery_Method_ShopData
	{
		return Delivery_Method_ShopData::get( $this->getDeliveryMethodId(), $this->getShop() );
	}

	public function setDeliveryMethodId( int $delivery_method_id ) : void
	{
		$this->delivery_method_id = $delivery_method_id;
	}

	public function getDeliveryPersonalTakeoverPlaceCode() : string
	{
		return $this->delivery_personal_takeover_place_code;
	}

	public function setDeliveryPersonalTakeoverPlaceCode( string $delivery_personal_takeover_place_code ) : void
	{
		$this->delivery_personal_takeover_place_code = $delivery_personal_takeover_place_code;
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

	public function getStatusId() : string
	{
		return $this->status_id;
	}

	public function setStatus(
		int $status_id,
		bool $customer_notified,
		string $comment,
		string $administrator,
		int $administrator_id,
		bool $comment_is_visible_for_customer,
		bool $save = false
	) : Order_Status_History
	{
		$this->status_id = $status_id;
		
		$history_item = new Order_Status_History();
		if($this->getId()) {
			$history_item->setOrderId( $this->getId() );
		}
		$history_item->setDateAdded( Data_DateTime::now() );
		$history_item->setStatusIs( $status_id );
		$history_item->setCustomerNotified( $customer_notified );
		$history_item->setComment( $comment );
		$history_item->setAdministrator( $administrator );
		$history_item->setAdministratorId( $administrator_id );
		$history_item->setCommentIsVisibleForCustomer( $comment_is_visible_for_customer );
		
		$this->status_history[] = $history_item;
		
		if($this->getId() && $save) {
			static::updateData(['status_id'=>$status_id], ['id'=>$this->id]);
			$history_item->save();
		}
		
		return $history_item;
	}
	
	/**
	 * @return Order_Status_History[]
	 */
	public function getStatusHistory(): array
	{
		return $this->status_history;
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

		$this->total_amount = 0.0;
		$this->product_amount = 0.0;
		$this->service_amount = 0.0;
		$this->delivery_amount = 0.0;
		$this->payment_amount = 0.0;

		$this->all_items_available = true;

		foreach( $this->items as $order_item ) {
			if(
				(
					$order_item->getType()==Order_Item::ITEM_TYPE_PRODUCT ||
					$order_item->getType()==Order_Item::ITEM_TYPE_GIFT
				)
				&&
				!$order_item->isAvailable()
			) {
				$this->all_items_available = false;
			}

			$amount = $order_item->getTotalAmount();

			$this->total_amount += $amount;

			switch($order_item->getType()) {
				case Order_Item::ITEM_TYPE_GIFT:
				break;
				case Order_Item::ITEM_TYPE_PRODUCT:
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
		if($this->getIsNew()) {
			$this->generateKey();
		}
	}
	
	public function afterAdd(): void
	{
		$this->generateNumber();
		static::updateData(
			[
				'number'=>$this->number
			],
			[
				'id'=>$this->id
			]);
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
	 * @return Order[]
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
		
		foreach(Discounts::Manager()->getActiveModules() as $dm) {
			$dm->Order_saved( $this );
		}

		$this->event( 'NewOrderSave' )->handleImmediately();

	}

	public function event( string $event ) : Order_Event
	{
		/**
		 * @var Order $this
		 */
		$e = Order_Event::newEvent( $this, $event );

		return $e;
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
	
}
