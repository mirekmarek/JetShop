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

	protected static string $MANAGE_MODULE = 'Admin.Delivery.Methods';

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
			$shop_id = $shop->getId();

			if(!isset($this->shop_data[$shop_id])) {

				$sh = new Delivery_Method_ShopData();
				$sh->setDeliveryMethodCode($this->code);
				$sh->setShopId($shop_id);

				$this->shop_data[$shop_id] = $sh;
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
		return $this->catchForm( $this->getEditForm() );
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
		return $this->catchForm( $this->getAddForm() );
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
	 * @return string
	 */
	public function getKind(): string
	{
		return $this->kind;
	}

	/**
	 * @param string $kind
	 */
	public function setKind( string $kind ): void
	{
		$this->kind = $kind;
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

	public function getShopData( string|null $shop_id=null ) : Delivery_Method_ShopData|null
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return $this->shop_data[$shop_id];
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

	public function isPersonalTakeOver() : bool
	{
		return $this->kind == Delivery_Kind::KIND_PERSONAL_TAKEOVER;
	}

	public function isEDelivery() : bool
	{
		return $this->kind == Delivery_Kind::KIND_E_DELIVERY;
	}


	public function getPersonalTakeOverPlace( string $shop_id, string $place_code, $only_active=true ) : ?Delivery_PersonalTakeover_Place
	{
		if(!$this->isPersonalTakeOver()) {
			return null;
		}

		$place = Delivery_PersonalTakeover_Place::getPlace( $shop_id, $this->code, $place_code );

		if(!$place) {
			return null;
		}

		if($only_active && !$place->isActive()) {
			return null;
		}

		return $place;
	}

	public function hasPersonalTakeOverPlace( string $shop_id, string $place_code, $only_active=true ) : bool
	{
		return (bool)$this->getPersonalTakeOverPlace( $shop_id, $place_code, $only_active );
	}

	public static function getScope() : array
	{
		if(static::$scope===null) {
			$list = Delivery_Method::getList();

			static::$scope = [];

			foreach($list as $kind) {
				static::$scope[$kind->getCode()] = $kind->getInternalName();
			}
		}

		return static::$scope;
	}

}
