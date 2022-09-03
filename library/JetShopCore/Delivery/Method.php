<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Exception;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_method',
	database_table_name: 'delivery_methods',
	id_controller_class: DataModel_IDController_Passive::class,
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
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Code:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter code'
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
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		is_required: true,
		label: 'Kind:',
		select_options_creator: [
			Delivery_Kind::class,
			'getScope'
		],
		error_messages: [
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
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		is_required: true,
		label: 'Internal name:',
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please enter internal name'
		]
	)]
	protected string $internal_name = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal description:'
	)]
	protected string $internal_description = '';


	/**
	 * @var Delivery_Method_Class[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_Class::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Classes:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_EMPTY => 'Please select delivery class',
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select delivery class'
		],
		default_value_getter_name: 'getDeliveryClassCodes',
		select_option_creator: [Delivery_Class::class, 'getScope']
	)]
	protected array $delivery_classes = [];

	
	
	/**
	 * @var Delivery_Method_PaymentMethods[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_PaymentMethods::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Payment methods:',
		is_required: true,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select payment method',
			Form_Field::ERROR_CODE_EMPTY => 'Please select payment method'
		],
		default_value_getter_name: 'getPaymentMethodsCodes',
		select_option_creator: [Payment_Method::class, 'getScope']
	)]
	protected array $payment_methods = [];

	

	/**
	 * @var Delivery_Method_Services[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_Services::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Services:',
		is_required: false,
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select service',
		],
		default_value_getter_name: 'getServicesCodes',
		select_option_creator: [Services_Service::class, 'getDeliveryServicesScope']
	)]
	protected array $services = [];
	
	

	/**
	 * @var Delivery_Method_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Method_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

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
		Delivery_Method_ShopData::checkShopData( $this, $this->shop_data );
	}


	/**
	 * @return Form
	 */
	public function getEditForm() : Form
	{
		if(!$this->_form_edit) {
			$this->_form_edit = $this->createForm('edit_form');
			$this->_form_edit->getField('code')->setIsReadonly(true);
		}
		
		return $this->_form_edit;
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
			$this->_form_add = $this->createForm('add_form');

			$code = $this->_form_add->getField('code');
			$code->setErrorMessages([
				'exists' => 'Delivery method with the same name already exists'
			]);

			$code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if(!$value) {
					$field->setError( Form_Field::ERROR_CODE_EMPTY );
					return false;
				}

				$exists = Delivery_Method::get($value);

				if($exists) {
					$field->setError('exists');

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
		$this->code = $value;
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


	public function getShopData( ?Shops_Shop $shop=null ) : Delivery_Method_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
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
		foreach($this->delivery_classes as $r) {
			if(!in_array($r->getClassCode(), $codes)) {
				$r->delete();
				unset($this->delivery_classes[$r->getClassCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Delivery_Class::get( $code )) ) {
				continue;
			}

			if(!isset($this->delivery_classes[$r->getCode()])) {
				$new_item = new Delivery_Method_Class();
				$new_item->setMethodCode($this->getCode());
				$new_item->setClassCode($code);

				$this->delivery_classes[$code] = $new_item;
				$new_item->save();
			}
		}
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
		$res = [];

		foreach( $this->delivery_classes as $code=>$rec ) {
			$rec = $rec->getClass();
			if($rec) {
				$res[$code] = $rec;
			}
		}

		return $res;
	}

	public function isPersonalTakeover() : bool
	{
		return $this->kind == Delivery_Kind::KIND_PERSONAL_TAKEOVER;
	}

	public function isEDelivery() : bool
	{
		return $this->kind == Delivery_Kind::KIND_E_DELIVERY;
	}


	public function getPersonalTakeoverPlace( Shops_Shop $shop, string $place_code, $only_active=true ) : ?Delivery_PersonalTakeover_Place
	{
		if(!$this->isPersonalTakeover()) {
			return null;
		}

		$place = Delivery_PersonalTakeover_Place::getPlace( $shop, $this->code, $place_code );

		if(!$place) {
			return null;
		}

		if($only_active && !$place->isActive()) {
			return null;
		}

		return $place;
	}

	public function hasPersonalTakeoverPlace( Shops_Shop $shop, string $place_code, $only_active=true ) : bool
	{
		return (bool)$this->getPersonalTakeoverPlace( $shop, $place_code, $only_active );
	}










	public function setPaymentMethods( array $codes ) : void
	{
		foreach($this->payment_methods as $r) {
			if(!in_array($r->getPaymentMethodCode(), $codes)) {
				$r->delete();
				unset($this->payment_methods[$r->getPaymentMethodCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Payment_Method::get( $code )) ) {
				continue;
			}

			if(!isset($this->payment_methods[$r->getCode()])) {
				$new_item = new Delivery_Method_PaymentMethods();
				$new_item->setDeliveryMethodCode($this->getCode());
				$new_item->setPaymentMethodCode($code);

				$this->payment_methods[$code] = $new_item;
				$new_item->save();
			}
		}
	}

	public function getPaymentMethodsCodes() : array
	{
		return array_keys( $this->getPaymentMethods() );
	}

	/**
	 *
	 * @return Payment_Method[]
	 */
	public function getPaymentMethods() : iterable
	{
		$res = [];

		foreach( $this->payment_methods as $code=>$item ) {
			if(($item = $item->getPaymentMethod())) {
				$res[$code] = $item;
			}
		}

		return $res;
	}









	public function setServices( array $codes ) : void
	{
		foreach($this->services as $r) {
			if(!in_array($r->getServiceCode(), $codes)) {
				$r->delete();
				unset($this->services[$r->getServiceCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Payment_Method::get( $code )) ) {
				continue;
			}

			if(!isset($this->services[$r->getCode()])) {
				$new_item = new Delivery_Method_Services();
				$new_item->setDeliveryMethodCode($this->getCode());
				$new_item->setServiceCode($code);

				$this->services[$code] = $new_item;
				$new_item->save();
			}
		}
	}

	public function getServicesCodes() : array
	{
		return array_keys($this->getServices());
	}

	/**
	 *
	 * @return Services_Service[]
	 */
	public function getServices() : iterable
	{
		$res = [];
		foreach($this->services as $code=>$item) {
			if(($item = $item->getService())) {
				$res[$code] = $item;
			}
		}

		return $res;
	}
	
	public function getOrderItem( CashDesk $cash_desk ) : Order_Item
	{
		$shd = $this->getShopData($cash_desk->getShop());

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
			return $this->getShopData( $order->getShop() )->getConfirmationEmailInfoText();
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
