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
use Jet\Form_Field_FileImage;
use Jet\Form_Field_Textarea;
use Jet\Http_Request;
use JetApplication\Complaint_ComplaintType;
use JetApplication\Complaint_DeliveryOfClaimedGoods;
use JetApplication\Complaint_PreferredSolution;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_Admin_Trait;
use JetApplication\Admin_Managers_Complaint;
use JetApplication\Complaint_Event;
use JetApplication\Complaint_Image;
use JetApplication\Context_ProvidesContext_Interface;
use JetApplication\Context_ProvidesContext_Trait;
use JetApplication\Customer;
use JetApplication\Customer_Address;
use JetApplication\Delivery_Method;
use JetApplication\EShopEntity_HasEvents_Interface;
use JetApplication\EShopEntity_HasEvents_Trait;
use JetApplication\EShopEntity_HasGet_Interface;
use JetApplication\EShopEntity_HasGet_Trait;
use JetApplication\EShopEntity_HasOrderContext_Interface;
use JetApplication\EShopEntity_HasOrderContext_Trait;
use JetApplication\EShopEntity_HasStatus_Interface;
use JetApplication\EShopEntity_HasStatus_Trait;
use JetApplication\EShopEntity_WithEShopRelation;
use JetApplication\Complaint;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use JetApplication\EShopEntity_HasNumberSeries_Trait;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\EShop_Pages;
use JetApplication\EShop;
use JetApplication\Complaint_Trait_Events;
use JetApplication\Complaint_Trait_Status;
use JetApplication\Complaint_Trait_Changes;

#[DataModel_Definition(
	name: 'complaint',
	database_table_name: 'complaints',
	key: [
		'name' => 'key',
		'property_names' => [
			'key'
		],
		'type' => DataModel::KEY_TYPE_UNIQUE
	]
)]
#[EShopEntity_Definition(
	entity_name_readable: 'Complaint',
	admin_manager_interface: Admin_Managers_Complaint::class
)]
abstract class Core_Complaint extends EShopEntity_WithEShopRelation implements
	EShopEntity_HasGet_Interface,
	EShopEntity_HasNumberSeries_Interface,
	EShopEntity_HasStatus_Interface,
	EShopEntity_HasEvents_Interface,
	EShopEntity_HasOrderContext_Interface,
	Context_ProvidesContext_Interface,
	EShopEntity_Admin_Interface
{
	use Context_ProvidesContext_Trait;
	
	use EShopEntity_HasGet_Trait;
	use EShopEntity_HasNumberSeries_Trait;
	use EShopEntity_HasStatus_Trait;
	use EShopEntity_HasEvents_Trait;
	use EShopEntity_HasOrderContext_Trait;
	
	use Complaint_Trait_Status;
	use Complaint_Trait_Events;
	use Complaint_Trait_Changes;
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
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Delivery of the claimed goods:',
		select_options_creator: [Complaint_DeliveryOfClaimedGoods::class, 'getScope']
	)]
	protected string $delivery_of_claimed_goods_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Complaint type:',
		select_options_creator: [Complaint_ComplaintType::class, 'getScope']
	)]
	protected string $complaint_type_code = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 50,
		is_key: true
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Preferred solution:',
		select_options_creator: [Complaint_PreferredSolution::class, 'getScope']
	)]
	protected string $preferred_solution_code = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Service report:',
	)]
	protected string $service_report = '';
	
	protected ?Form $upload_images_form = null;
	
	
	public static function getNumberSeriesEntityIsPerShop() : bool
	{
		return true;
	}
	
	public static function getNumberSeriesEntityTitle() : string
	{
		return 'Complaint';
	}
	
	
	public static function startNew(
		Order $order,
		Product_EShopData $product,
		string $problem_description
	) : static
	{
		$complaint = new static();
		
		$complaint->setEshop( $order->getEshop() );
		
		$complaint->setOrder( $order );
		
		$complaint->setCustomerId( $order->getCustomerId() );
		$complaint->setDeliveryAddress( $order->getDeliveryAddress() );
		$complaint->setEmail( $order->getEmail() );
		$complaint->setPhone( $order->getPhone() );
		
		$complaint->setDateStarted( Data_DateTime::now() );
		$complaint->setIpAddress( Http_Request::clientIP() );
		
		$complaint->setProblemDescription( $problem_description );
		$complaint->setProductId( $product->getId() );
		
		$complaint->save();
		
		$complaint->newUnfinishedComplaint();
		
		return $complaint;
	}
	
	public function getURL() : string
	{
		return EShop_Pages::Complaints( $this->getEshop() )->getURL( GET_params: [
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
		return $this->delivery_personal_takeover_delivery_point_code??'';
	}
	
	public function setDeliveryPersonalTakeoverDeliveryPointCode( string $delivery_personal_takeover_delivery_point_code ) : void
	{
		$this->delivery_personal_takeover_delivery_point_code = $delivery_personal_takeover_delivery_point_code;
	}
	
	public function getDeliveryOfClaimedGoodsCode(): string
	{
		return $this->delivery_of_claimed_goods_code??'';
	}
	
	public function getDeliveryOfClaimedGoods() : ?Complaint_DeliveryOfClaimedGoods
	{
		return Complaint_DeliveryOfClaimedGoods::get( $this->getDeliveryOfClaimedGoodsCode() );
	}
	
	public function setDeliveryOfClaimedGoodsCode( string $delivery_of_claimed_goods_code ): void
	{
		$this->delivery_of_claimed_goods_code = $delivery_of_claimed_goods_code;
	}
	
	public function getComplaintTypeCode(): string
	{
		return $this->complaint_type_code??'';
	}
	
	public function getComplaintType() : ?Complaint_ComplaintType
	{
		return Complaint_ComplaintType::get( $this->getComplaintTypeCode() );
	}
	
	public function setComplaintTypeCode( string $complaint_type_code ): void
	{
		$this->complaint_type_code = $complaint_type_code;
	}
	
	public function getPreferredSolutionCode(): string
	{
		return $this->preferred_solution_code;
	}
	
	public function setPreferredSolutionCode( string $preferred_solution_code ): void
	{
		$this->preferred_solution_code = $preferred_solution_code;
	}
	
	public function getPreferredSolution() : ?Complaint_PreferredSolution
	{
		return Complaint_PreferredSolution::get( $this->getPreferredSolutionCode() );
	}
	
	public function getServiceReport(): string
	{
		return $this->service_report;
	}
	
	public function setServiceReport( string $service_report ): void
	{
		$this->service_report = $service_report;
	}
	

	
	/**
	 * @return Complaint_Event[]
	 */
	public function getHistory() : array
	{
		return Complaint_Event::getEventsList( $this->getId() );
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
	
	
	public static function getByKey( string $key ) : ?Complaint
	{
		$complaints = Complaint::fetch(['complaint' => [
			'key' => $key
		]]);
		
		if(count($complaints)!=1) {
			return null;
		}
		
		return $complaints[0];
	}
	
	public static function getByNumber( string $number, EShop $eshop ) : Complaint|null
	{
		$where = $eshop->getWhere();
		$where[] = 'AND';
		$where[] = [
			'number' => $number
		];
		
		$complaints = Complaint::fetch(['' => $where]);
		
		if(count($complaints)!=1) {
			return null;
		}
		
		return $complaints[0];
	}
	
	
	public static function getByURL() : ?Complaint
	{
		$GET = Http_Request::GET();
		
		$complaint_key = $GET->getString('c');
		$complaint_secondary_key = $GET->getString('k');
		
		if(
			$complaint_key &&
			$complaint_secondary_key &&
			($complaint = Complaint::getByKey( $complaint_key )) &&
			($complaint->getSecondKey()==$complaint_secondary_key)
		) {
			return $complaint;
		}
		
		return null;
	}
	
	/**
	 * @return Complaint[]
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
		
		return static::fetch( ['complaint'=>$where], order_by: ['-id']  );
		
	}
	
	
	/**
	 * @return static[]
	 */
	public static function getByOrder( Order $order ) : array
	{
		$where = $order->getEshop()->getWhere();
		$where[] = 'AND';
		$where['order_id'] = $order->getId();
		
		return static::fetch( ['complaint'=>$where], order_by: ['-id'] );
		
	}
	
	/**
	 * @return static[]
	 */
	public static function getByCustomer( Customer $customer ) : array
	{
		$where = $customer->getEshop()->getWhere();
		$where[] = 'AND';
		$where['customer_id'] = $customer->getId();
		
		return static::fetch( ['complaint'=>$where], order_by: ['-id']  );
		
	}
	
	protected ?Form $problem_description_edit_form = null;
	
	public function getProblemDescriptionEditForm() : Form
	{
		if(!$this->problem_description_edit_form) {
			$this->problem_description_edit_form = $this->createForm('edit_form', [
				'delivery_of_claimed_goods_code',
				'complaint_type_code',
				'preferred_solution_code',
				'problem_description'
			]);
		}
		
		return $this->problem_description_edit_form;
	}
	
	protected ?array $images = null;
	
	/**
	 * @return Complaint_Image[]
	 */
	public function getImages() : array
	{
		if($this->images===null) {
			/**
			 * @var Complaint $this
			 */
			$this->images = Complaint_Image::getForComplaint( $this );
		}
		
		return $this->images;
	}
	
	
	public function getUploadImagesForm() : ?Form
	{
		if(!$this->isEditable()) {
			return null;
		}
		
		if(!$this->upload_images_form) {
			$image_field = new Form_Field_FileImage('image', '');
			$image_field->setErrorMessages([
				Form_Field_FileImage::ERROR_CODE_INVALID_FORMAT => 'Please upload image',
				Form_Field_FileImage::ERROR_CODE_FILE_IS_TOO_LARGE => 'Sorry, bude the file is too large',
				Form_Field_FileImage::ERROR_CODE_DISALLOWED_FILE_TYPE => 'Please upload image',
			]);
			$image_field->setAllowMultipleUpload( true );
			
			$this->upload_images_form = new Form('upload_images_form', [
				$image_field
			]);
		}
		
		return $this->upload_images_form;
	}
	
	public function handleImageUpload() : void
	{
		$form = $this->getUploadImagesForm();
		if($form->catch()) {
			/**
			 * @var Form_Field_FileImage $image_field
			 */
			$image_field = $form->field('image');
			$images = $image_field->getValidFiles();
			foreach($images as $img) {
				/**
				 * @var Complaint $this
				 */
				Complaint_Image::uploadImage( $this, $img );
				$this->images = null;
			}
		}
	}
	
	public function deleteImage( int $image_id ) : void
	{
		$images = $this->getImages();
		if(isset($images[$image_id])) {
			if(!$images[$image_id]->isLocked()) {
				$images[$image_id]->delete();
				$this->images = null;
			}
		}
	}

	public function handleShowImage() : void
	{
		if( ($img_id = Http_Request::GET()->getInt('img')) ) {
			$images = $this->getImages();
			if(isset($images[$img_id])) {
				$images[$img_id]->show();
			}
		}
		
		if( ($img_id = Http_Request::GET()->getInt('thb')) ) {
			$images = $this->getImages();
			if(isset($images[$img_id])) {
				$images[$img_id]->showThb();
			}
		}
		
		
	}
	
	public function getMinimalImageCount() : int
	{
		return 3;
	}
	
	public function getMinimalProblemDescriptionLength() : int
	{
		return 100;
	}
	
	public function canBeFinished() : bool
	{
		if(strlen($this->problem_description)<$this->getMinimalProblemDescriptionLength()) {
			return false;
		}
		
		if(count($this->getImages())<$this->getMinimalImageCount()) {
			return false;
		}
		
		return true;
	}
	
	public function finish() : void
	{
		foreach($this->getImages() as $img) {
			$img->setLocked( true );
			$img->save();
		}
		
		$this->newComplaintFinished();
	}
	
	public function setEditable( bool $editable ): void
	{
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
