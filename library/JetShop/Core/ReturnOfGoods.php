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
use Jet\Form_Field_Textarea;
use Jet\Http_Request;
use JetApplication\Admin_Managers_ReturnOfGoods;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\EShopEntity_HasOrderContext_Interface;
use JetApplication\EShopEntity_HasOrderContext_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\ReturnOfGoods_Event;
use JetApplication\Customer;
use JetApplication\Customer_Address;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\ReturnOfGoods;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Pages;
use JetApplication\EShop;
use JetApplication\ReturnOfGoods_Trait_Changes;
use JetApplication\ReturnOfGoods_Trait_Status;
use JetApplication\ReturnOfGoods_Trait_Events;

#[DataModel_Definition(
	name: 'return_of_goods',
	database_table_name: 'returns_of_goods',
	key: [
		'name' => 'key',
		'property_names' => [
			'key'
		],
		'type' => DataModel::KEY_TYPE_UNIQUE
	]
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Return of goods',
	admin_manager_interface: Admin_Managers_ReturnOfGoods::class
)]
abstract class Core_ReturnOfGoods extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface,
	EShopEntity_HasNumberSeries_Interface,
	EShopEntity_HasOrderContext_Interface,
	EShopEntity_Admin_Interface,
	Context_ProvidesContext_Interface
{
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_HasEvents_Trait;
	use EShopEntity_HasOrderContext_Trait;
	
	use Context_ProvidesContext_Trait;
	
	use ReturnOfGoods_Trait_Status;
	use ReturnOfGoods_Trait_Events;
	use ReturnOfGoods_Trait_Changes;
	
	use EShopEntity_Admin_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description of the problem:',
		error_messages: [
			Form_Field_Textarea::ERROR_CODE_EMPTY => 'Please enter description of the problem'
		]
	)]
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 9999999
	)]
	protected string $problem_description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
	)]
	protected string $key = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_key: true,
	)]
	protected string $ip_address = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_DATE_TIME
	)]
	protected ?Data_DateTime $date_started = null;
	
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
	
	protected ?Form $upload_images_form = null;
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return true;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Return of goods';
	}
	
	
	public static function startNew(
		Order $order,
		Product_EShopData $product,
		string $problem_description
	) : static
	{
		$return = new static();
		
		$return->setEshop( $order->getEshop() );
		$return->setOrder( $order );
		
		$return->setCustomerId( $order->getCustomerId() );
		$return->setDeliveryAddress( $order->getDeliveryAddress() );
		$return->setEmail( $order->getEmail() );
		$return->setPhone( $order->getPhone() );
		
		$return->setDateStarted( Data_DateTime::now() );
		$return->setIpAddress( Http_Request::clientIP() );
		
		$return->setProblemDescription( $problem_description );
		$return->setProductId( $product->getId() );
		
		$return->save();
		
		$return->newUnfinishedReturnOfGoods();
		
		return $return;
	}
	
	public function getURL() : string
	{
		return EShop_Pages::ReturnOfGoods( $this->getEshop() )->getURL( GET_params: [
			'c' => $this->getKey(),
			'k' => $this->getSecondKey()
		] );
	}
	
	
	public function getProductId(): int
	{
		return $this->product_id;
	}
	
	public function setProductId( int $product_id ): void
	{
		$this->product_id = $product_id;
	}
	
	public function getProduct() : ?Product_EShopData
	{
		return Product_EShopData::get( $this->product_id, $this->getEshop() );
	}
	
	public function getProblemDescription(): string
	{
		return $this->problem_description;
	}
	
	public function setProblemDescription( string $problem_description ): void
	{
		$this->problem_description = $problem_description;
	}
	
	
	
	
	public function getId() : int
	{
		return $this->id;
	}
	
	public function setId( int $id ) : void
	{
		$this->id = $id;
	}
	
	
	
	
	public function getNumberSeriesEntityData(): ?Data_DateTime
	{
		return $this->getDateStarted();
	}
	
	public function getNumberSeriesEntityShop(): ?EShop
	{
		return $this->getEshop();
	}
	
	
	
	public function getIpAddress() : string
	{
		return $this->ip_address;
	}
	
	public function setIpAddress( string $ip_address ) : void
	{
		$this->ip_address = $ip_address;
	}
	
	public function getDateStarted() : Data_DateTime
	{
		return $this->date_started;
	}
	
	public function setDateStarted( Data_DateTime $date_started ) : void
	{
		$this->date_started = $date_started;
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
	
	public function getDeliveryMethodId() : int
	{
		return $this->delivery_method_id;
	}
	
	public function getDeliveryMethod() : Delivery_Method
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
	
	
	
	
	
	

	
	/**
	 * @return ReturnOfGoods_Event[]
	 */
	public function getHistory() : array
	{
		return ReturnOfGoods_Event::getEventsList( $this->getId() );
	}
	
	
	
	protected function generateKey() : void
	{
		$this->key = md5( time().uniqid().uniqid() );
	}
	
	public function getKey() : string
	{
		return $this->key;
	}
	
	public function getSecondKey() : string
	{
		return sha1( $this->email );
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
	}
	
	
	public static function getByKey( string $key ) : ?ReturnOfGoods
	{
		$returns = ReturnOfGoods::fetch(['return_of_goods' => [
			'key' => $key
		]]);
		
		if(count($returns)!=1) {
			return null;
		}
		
		return $returns[0];
	}
	
	public static function getByURL() : ?ReturnOfGoods
	{
		$GET = Http_Request::GET();
		
		$return_key = $GET->getString('c');
		$return_secondary_key = $GET->getString('k');
		
		if(
			$return_key &&
			$return_secondary_key &&
			($return = static::getByKey( $return_key )) &&
			($return->getSecondKey()==$return_secondary_key)
		) {
			return $return;
		}
		
		return null;
	}
	
	/**
	 * @return ReturnOfGoods[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
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
	
	
	/**
	 * @param Order $order
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getByOrderItem( Order $order, int $product_id ) : array
	{
		$where = $order->getEshop()->getWhere();
		$where[] = 'AND';
		$where['order_id'] = $order->getId();
		$where[] = 'AND';
		$where['product_id'] = $product_id;
		
		return static::fetch( ['return_of_goods'=>$where], order_by: ['-id']  );
		
	}
	
	
	/**
	 * @return static[]
	 */
	public static function getByOrder( Order $order ) : array
	{
		$where = $order->getEshop()->getWhere();
		$where[] = 'AND';
		$where['order_id'] = $order->getId();
		
		return static::fetch( ['return_of_goods'=>$where], order_by: ['-id']  );
		
	}
	
	/**
	 * @return static[]
	 */
	public static function getByCustomer( Customer $customer ) : array
	{
		$where = $customer->getEshop()->getWhere();
		$where[] = 'AND';
		$where['customer_id'] = $customer->getId();
		
		return static::fetch( ['return_of_goods'=>$where], order_by: '-id' );
		
	}
	
	protected ?Form $problem_description_edit_form = null;
	
	public function getProblemDescriptionEditForm() : Form
	{
		if(!$this->problem_description_edit_form) {
			$this->problem_description_edit_form = $this->createForm('edit_form', [
				'problem_description'
			]);
		}
		
		return $this->problem_description_edit_form;
	}
	
	protected ?array $images = null;
	
	
	public function getMinimalProblemDescriptionLength() : int
	{
		return 10;
	}
	
	public function canBeFinished() : bool
	{
		if(strlen($this->problem_description)<$this->getMinimalProblemDescriptionLength()) {
			return false;
		}
		
		return true;
	}
	
	public function finish() : void
	{
		$this->newReturnOfGoodsFinished();
	}
	
	public function isEditable(): bool
	{
		if(
			$this->cancelled
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
