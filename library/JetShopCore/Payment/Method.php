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
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_Related_1toN_Iterator;
use Jet\Form_Field_MultiSelect;
use Jet\Form_Field_Select;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'payment_method',
	database_table_name: 'payment_methods',
	id_controller_class: DataModel_IDController_Name::class,
	id_controller_options: [
		'id_property_name' => 'code',
		'get_name_method_name' => 'getCode'
	]
)]
abstract class Core_Payment_Method extends DataModel
{
	protected static string $MANAGE_MODULE = 'Admin.Payment.Methods';

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
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 255,
		form_field_type: 'Select',
		form_field_is_required: true,
		form_field_label: 'Kind:',
		form_field_get_select_options_callback: [Payment_Kind::class, 'getScope'],
		form_field_error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';


	/**
	 * @var Payment_Method_ShopData[]|DataModel_Related_1toN|DataModel_Related_1toN_Iterator|null
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_ShopData::class
	)]
	protected $shop_data = null;


	/**
	 * @var Auth_Administrator_User_Roles|DataModel_Related_MtoN_Iterator|Delivery_Method[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_DeliveryMethods::class,
		form_field_creator_method_name: 'createDeliveryMethodInputField',
	)]
	protected $delivery_methods = null;


	/**
	 * @var Auth_Administrator_User_Roles|DataModel_Related_MtoN_Iterator|Services_Service[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Payment_Method_Services::class,
		form_field_creator_method_name: 'createServicesInputField',
	)]
	protected $services = null;


	/**
	 * @var ?Form
	 */
	protected ?Form $_form_edit = null;

	/**
	 * @var ?Form
	 */
	protected ?Form $_form_add = null;

	protected static ?array $scope = null;

	/**
	 * @return string
	 */
	public static function getManageModuleName(): string
	{
		return self::$MANAGE_MODULE;
	}

	/**
	 * @param string $name
	 */
	public static function setManageModuleName( string $name ): void
	{
		self::$MANAGE_MODULE = $name;
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

				$sh = new Payment_Method_ShopData();
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

				$exists = Payment_Method::get($value);

				if($exists) {
					$field->setCustomError(
						Tr::_('Payment method with the same name already exists')
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
	 * @return iterable
	 */
	public static function getList() : iterable
	{
		$where = [];

		return static::fetchInstances( $where );
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

	public function getKind() : ?Payment_Kind
	{
		return Payment_Kind::get( $this->kind );
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

	public function getShopData( string|null $shop_code=null ) : Payment_Method_ShopData|null
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return $this->shop_data[$shop_code];
	}

	public function getEditURL() : string
	{
		return Payment_Method::getPaymentMethodEditURL( $this->getCode() );
	}

	public static function getPaymentMethodEditURL( string $code ) : string
	{
		/**
		 * @var Payment_Method_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Sticker::getManageModuleName() );

		return $module->getPaymentMethodEditURL( $code );
	}

	public static function getScope() : array
	{
		if(static::$scope===null) {
			$list = Payment_Method::getList();

			static::$scope = [];

			foreach($list as $item) {
				static::$scope[$item->getCode()] = $item->getInternalName();
			}
		}

		return static::$scope;
	}




	public function setDeliveryMethods( array $codes ) : void
	{
		$methods = [];

		foreach( $codes as $code ) {

			$class = Delivery_Method::get( $code );

			if( !$class ) {
				continue;
			}

			$methods[] = $class;
		}
		$this->delivery_methods->setItems( $methods );

	}

	public function getDeliveryMethodsCodes() : array
	{
		$codes = [];

		foreach($this->getDeliveryMethods() as $method) {
			$codes[] = $method->getCode();
		}

		return $codes;
	}

	/**
	 *
	 * @return Delivery_Method[]
	 */
	public function getDeliveryMethods() : iterable
	{
		return $this->delivery_methods;
	}

	public function createDeliveryMethodInputField() : Form_Field_MultiSelect
	{
		$input = new Form_Field_MultiSelect('delivery_methods', 'Delivery methods:', $this->getDeliveryMethodsCodes(), true );

		$input->setErrorMessages([
			Form_Field_MultiSelect::ERROR_CODE_INVALID_VALUE => 'Please select delivery method',
			Form_Field_MultiSelect::ERROR_CODE_EMPTY => 'Please select delivery method'
		]);

		$input->setSelectOptions(Delivery_Method::getScope());

		$input->setCatcher( function($value) {
			$this->setDeliveryMethods( $value );
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

		$input->setSelectOptions( Services_Service::getScope( Services_Kind::KIND_PAYMENT ) );

		$input->setCatcher( function($value) {
			$this->setServices( $value );
		} );

		return $input;
	}

	public function getOrderItem( CashDesk $cash_desk ) : Order_Item
	{
		$shd = $this->getShopData($cash_desk->getShopCode());

		$item = new Order_Item();
		$item->setType( Order_Item::ITEM_TYPE_PAYMENT );
		$item->setQuantity( 1 );
		$item->setTitle( $shd->getTitle() );
		$item->setCode( $this->getCode() );
		$item->setPricePerItem( $shd->getDefaultPrice() );
		$item->setVatRate( $shd->getVatRate() );
		$item->setDescription( $shd->getDescriptionShort() );

		return $item;
	}

}
