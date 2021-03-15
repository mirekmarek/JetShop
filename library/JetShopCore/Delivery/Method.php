<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Name;
use Jet\DataModel_Related_MtoN_Iterator;
use Jet\Exception;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_Related_1toN_Iterator;
use Jet\Form_Field_Select;
use Jet\Tr;
use Jet\Form_Field_MultiSelect;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method',
	database_table_name: 'delivery_methods',
	id_controller_class: DataModel_IDController_Name::class,
	id_controller_options: [
		'id_property_name' => 'code',
		'get_name_method_name' => 'getCode'
	]
)]
abstract class Core_Delivery_Method extends DataModel
{

	protected static string $manage_module_name = 'Admin.Delivery.Methods';
	protected static string $method_module_name_prefix = 'Order.Delivery.Methods.';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_ID,
		is_id: true,
		form_field_type: 'Input',
		form_field_is_required: true,
		form_field_label: 'Code:',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter code'
		]
	)]
	protected string $code = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
		form_field_type: 'Select',
		form_field_is_required: true,
		form_field_label: 'Kind:',
		form_field_get_select_options_callback: [
			Delivery_Kind::class,
			'getScope'
		],
		form_field_error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';

	/**
	 * @var string
	 */ 
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: 'Input',
		form_field_is_required: true,
		form_field_label: 'Internal name:',
		form_field_error_messages: [
			Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter internal name'
		]
	)]
	protected string $internal_name = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
		form_field_type: 'Input',
		form_field_label: 'Internal description:'
	)]
	protected string $internal_description = '';


	/**
	 * @var Auth_Administrator_User_Roles|DataModel_Related_MtoN_Iterator|Delivery_Method_Classes[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_Classes::class,
		form_field_creator_method_name: 'createClassInputField',
	)]
	protected $delivery_classes = null;

	/**
	 * @var Auth_Administrator_User_Roles|DataModel_Related_MtoN_Iterator|Payment_Method[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_PaymentMethods::class,
		form_field_creator_method_name: 'createPaymentMethodInputField',
	)]
	protected $payment_methods = null;


	/**
	 * @var Auth_Administrator_User_Roles|DataModel_Related_MtoN_Iterator|Services_Service[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_Services::class,
		form_field_creator_method_name: 'createServicesInputField',
	)]
	protected $services = null;


	/**
	 * @var Delivery_Method_ShopData[]|DataModel_Related_1toN|DataModel_Related_1toN_Iterator|null
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_ShopData::class
	)]
	protected $shop_data = null;

	protected static ?array $scope = null;

	/**
	 * @var ?Form
	 */
	protected ?Form $_form_edit = null;

	/**
	 * @var ?Form
	 */
	protected ?Form $_form_add = null;

	/**
	 * @return string
	 */
	public static function getManageModuleName(): string
	{
		return static::$manage_module_name;
	}

	/**
	 * @param string $name
	 */
	public static function setManageModuleName( string $name ): void
	{
		static::$manage_module_name = $name;
	}

	/**
	 * @return string
	 */
	public static function getMethodModuleNamePrefix(): string
	{
		return static::$method_module_name_prefix;
	}

	/**
	 * @param string $method_module_name_prefix
	 */
	public static function setMethodModuleNamePrefix( string $method_module_name_prefix ): void
	{
		static::$method_module_name_prefix = $method_module_name_prefix;
	}



	public function __construct()
	{
		parent::__construct();

		$this->afterLoad();
	}

	public function afterLoad() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_code = $shop->getCode();

			if(!isset($this->shop_data[$shop_code])) {

				$sh = new Delivery_Method_ShopData();
				$sh->setDeliveryMethodCode($this->code);
				$sh->setShopCode($shop_code);

				$this->shop_data[$shop_code] = $sh;
			}
		}
	}


	/**
	 * @return Form
	 */
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->getCommonForm('edit_form');
			$this->_form_edit->getField('code')->setIsReadonly(true);
		}
		
		return $this->_form_edit;
	}

	public function createClassInputField() : Form_Field_MultiSelect
	{
		$input = new Form_Field_MultiSelect('delivery_classes', 'Classes:', $this->getDeliveryClassCodes(), true );

		$input->setErrorMessages([
			Form_Field_MultiSelect::ERROR_CODE_EMPTY => 'Please select delivery class',
			Form_Field_MultiSelect::ERROR_CODE_INVALID_VALUE => 'Please select delivery class'
		]);

		$input->setSelectOptions(Delivery_Class::getScope());

		$input->setCatcher( function($value) {
			$this->setDeliveryClasses( $value );
		} );

		return $input;
	}


	/**
	 * @return bool
	 */
	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	/**
	 * @return Form
	 */
	public function getAddForm() : Form
	{
		if(!$this->_form_add) {
			$this->_form_add = $this->getCommonForm('add_form');

			$code = $this->_form_add->getField('code');

			$code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if(!$value) {
					$field->setError( Form_Field_Input::ERROR_CODE_EMPTY );
					return false;
				}

				$exists = Delivery_Method::get($value);

				if($exists) {
					$field->setCustomError(
						Tr::_('Delivery method with the same name already exists')
					);

					return false;
				}

				return true;
			});
		}
		
		return $this->_form_add;
	}

	/**
	 * @return bool
	 */
	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}

	/**
	 * @param string $code
	 * @return static|null
	 */
	public static function get( string $code ) : static|null
	{
		return static::load( $code );
	}

	/**
	 * @return Delivery_Method[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}

	public static function getScope() : array
	{
		if(static::$scope===null) {
			$list = Delivery_Method::getList();

			static::$scope = [];

			foreach($list as $item) {
				static::$scope[$item->getCode()] = $item->getInternalName();
			}
		}

		return static::$scope;
	}

	/**
	 * @param string $value
	 */
	public function setCode( string $value ) : void
	{
		$this->code = (string)$value;
	}

	/**
	 * @return string
	 */
	public function getCode() : string
	{
		return $this->code;
	}


	/**
	 * @param string $kind
	 */
	public function setKindCode( string $kind ): void
	{
		$this->kind = $kind;
	}

	/**
	 * @return string
	 */
	public function getKindCode(): string
	{
		return $this->kind;
	}

	public function getKind() : ?Delivery_Kind
	{
		return Delivery_Kind::get( $this->kind );
	}

	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind ? $kind->getTitle() : '';
	}


	/**
	 * @param string $value
	 */
	public function setInternalDescription( string $value ) : void
	{
		$this->internal_description = $value;
	}

	/**
	 * @return string
	 */
	public function getInternalDescription() : string
	{
		return $this->internal_description;
	}

	/**
	 * @param string $value
	 */
	public function setInternalName( string $value ) : void
	{
		$this->internal_name = $value;
	}

	/**
	 * @return string
	 */
	public function getInternalName() : string
	{
		return $this->internal_name;
	}

	public function getShopData( string|null $shop_code=null ) : Delivery_Method_ShopData|null
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return $this->shop_data[$shop_code];
	}

	public function getEditURL() : string
	{
		return Delivery_Method::getDeliveryMethodEditURL( $this->getCode() );
	}

	public static function getDeliveryMethodEditURL( string $code ) : string
	{
		/**
		 * @var Delivery_Method_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Sticker::getManageModuleName() );

		return $module->getDeliveryMethodEditURL( $code );
	}

	/**
	 * @param array $codes
	 */
	public function setDeliveryClasses( array $codes ) : void
	{
		$classes = [];

		foreach( $codes as $code ) {

			$class = Delivery_Class::get( $code );

			if( !$class ) {
				continue;
			}

			$classes[] = $class;
		}
		$this->delivery_classes->setItems( $classes );

	}

	/**
	 *
	 * @return array
	 */
	public function getDeliveryClassCodes() : array
	{
		$codes = [];

		foreach($this->getDeliveryClasses() as $class) {
			$codes[] = $class->getCode();
		}

		return $codes;
	}

	/**
	 *
	 * @return Delivery_Class[]
	 */
	public function getDeliveryClasses() : iterable
	{
		return $this->delivery_classes;
	}

	public function isPersonalTakeover() : bool
	{
		return $this->kind == Delivery_Kind::KIND_PERSONAL_TAKEOVER;
	}

	public function isEDelivery() : bool
	{
		return $this->kind == Delivery_Kind::KIND_E_DELIVERY;
	}


	public function getPersonalTakeoverPlace( string $shop_code, string $place_code, $only_active=true ) : ?Delivery_PersonalTakeover_Place
	{
		if(!$this->isPersonalTakeover()) {
			return null;
		}

		$place = Delivery_PersonalTakeover_Place::getPlace( $shop_code, $this->code, $place_code );

		if(!$place) {
			return null;
		}

		if($only_active && !$place->isActive()) {
			return null;
		}

		return $place;
	}

	public function hasPersonalTakeoverPlace( string $shop_code, string $place_code, $only_active=true ) : bool
	{
		return (bool)$this->getPersonalTakeoverPlace( $shop_code, $place_code, $only_active );
	}










	public function setPaymentMethods( array $codes ) : void
	{
		$methods = [];

		foreach( $codes as $code ) {

			$class = Payment_Method::get( $code );

			if( !$class ) {
				continue;
			}

			$methods[] = $class;
		}
		$this->payment_methods->setItems( $methods );

	}

	public function getPaymentMethodsCodes() : array
	{
		$codes = [];

		foreach($this->getPaymentMethods() as $method) {
			$codes[] = $method->getCode();
		}

		return $codes;
	}

	/**
	 *
	 * @return Payment_Method[]
	 */
	public function getPaymentMethods() : iterable
	{
		return $this->payment_methods;
	}

	public function createPaymentMethodInputField() : Form_Field_MultiSelect
	{
		$input = new Form_Field_MultiSelect('payment_methods', 'Payment methods:', $this->getPaymentMethodsCodes(), true );

		$input->setErrorMessages([
			Form_Field_MultiSelect::ERROR_CODE_INVALID_VALUE => 'Please select payment method',
			Form_Field_MultiSelect::ERROR_CODE_EMPTY => 'Please select payment method'
		]);

		$input->setSelectOptions(Payment_Method::getScope());

		$input->setCatcher( function($value) {
			$this->setPaymentMethods( $value );
		} );

		return $input;
	}








	public function setServices( array $codes ) : void
	{
		$services = [];

		foreach( $codes as $code ) {

			$class = Services_Service::get( $code );

			if( !$class ) {
				continue;
			}

			$services[] = $class;
		}
		$this->services->setItems( $services );

	}

	public function getServicesCodes() : array
	{
		$codes = [];

		foreach($this->getServices() as $service) {
			$codes[] = $service->getCode();
		}

		return $codes;
	}

	/**
	 *
	 * @return Services_Service[]
	 */
	public function getServices() : iterable
	{
		return $this->services;
	}

	public function createServicesInputField() : Form_Field_MultiSelect
	{
		$input = new Form_Field_MultiSelect('services', 'Services:', $this->getServicesCodes() );

		$input->setErrorMessages([
			Form_Field_MultiSelect::ERROR_CODE_INVALID_VALUE => 'Please select service',
		]);

		$input->setSelectOptions( Services_Service::getScope( Services_Kind::KIND_DELIVERY ) );

		$input->setCatcher( function($value) {
			$this->setServices( $value );
		} );

		return $input;
	}

	public function getOrderItem( CashDesk $cash_desk ) : Order_Item
	{
		$shd = $this->getShopData($cash_desk->getShopCode());

		$item = new Order_Item();
		$item->setType( Order_Item::ITEM_TYPE_DELIVERY );
		$item->setQuantity( 1 );
		$item->setTitle( $shd->getTitle() );
		$item->setCode( $this->getCode() );
		$item->setItemAmount( $shd->getDefaultPrice() );
		$item->setVatRate( $shd->getVatRate() );
		$item->setDescription( $shd->getDescriptionShort() );

		return $item;
	}

	public function getOrderConfirmationEmailInfoText( Order $order ) : string
	{
		/**
		 * @var Delivery_Method $this
		 */
		$module = $this->getModule();

		if($module) {
			return $module->getOrderConfirmationEmailInfoText( $order, $this );
		} else {
			$shop_code = $order->getShopCode();
			return $this->getShopData( $shop_code )->getConfirmationEmailInfoText();
		}
	}

	public function getModuleName() : string
	{
		return Delivery_Method::getMethodModuleNamePrefix().$this->getCode();
	}

	public function getModule(): null|Core_Delivery_Method_Module|Core_Delivery_Method_Module_PersonalTakeover
	{
		$module_name = $this->getModuleName();

		if(Application_Modules::moduleExists($module_name)) {
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return Application_Modules::moduleInstance( $module_name );
		}

		if($this->getKind()->moduleIsRequired()) {
			throw new Exception('Delivery module '.$module_name.' is required, but mussing (is not activated)');
		}


		return null;
	}

}
