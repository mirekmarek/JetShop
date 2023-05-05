<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Input;

use JetApplication\Delivery_Class_Method;
use JetApplication\Delivery_Class;
use JetApplication\Delivery_Method;
use JetApplication\Delivery_Class_ManageModuleInterface;
use JetApplication\Delivery_Kind;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_class',
	database_table_name: 'delivery_classes',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Class extends DataModel
{

	protected static string $manage_module_name = 'Admin.Delivery.Classes';

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
		max_len: 99999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal description:'
	)]
	protected string $internal_description = '';

	/**
	 * @var Delivery_Class_Method[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Class_Method::class,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_MULTI_SELECT,
		label: 'Methods:',
		default_value_getter_name: 'getDeliveryMethodCodes',
		error_messages: [
			Form_Field::ERROR_CODE_INVALID_VALUE => 'Please select delivery method'
		],
		select_options_creator: [Delivery_Method::class, 'getScope'],
		
	)]
	protected array $delivery_methods = [];
	

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
	 * @var Delivery_Class[]
	 */
	protected static array $loaded_items = [];
	
	/**
	 * @return string
	 */
	public static function getManageModuleName(): string
	{
		return static::$manage_module_name;
	}
	
	/**
	 * @param string $manage_module_name
	 */
	public static function setManageModuleName( string $manage_module_name ): void
	{
		static::$manage_module_name = $manage_module_name;
	}
	
	

	/**
	 * @return string
	 */
	public function getCode() : string
	{
		return $this->code;
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
				Form_Field::ERROR_CODE_EMPTY => 'Please add code',
				'exists' => 'Delivery class with the same name already exists'
			]);

			$code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if(!$value) {
					$field->setError( Form_Field::ERROR_CODE_EMPTY );
					return false;
				}

				$exists = Delivery_Class::get($value);

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
	 * @param int|string $code
	 * @return static|null
	 */
	public static function get( int|string $code ) : static|null
	{
		if(!isset( static::$loaded_items[$code])) {
			static::$loaded_items[$code] = static::load( $code );
		}
		return static::$loaded_items[$code];
	}

	/**
	 * @return Delivery_Class[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
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

	public function getEditURL() : string
	{
		return Delivery_Class::getDeliveryClassEditURL( $this->getCode() );
	}

	public static function getDeliveryClassEditURL( string $code ) : string
	{
		/**
		 * @var Delivery_Class_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Delivery_Class::getManageModuleName() );

		return $module->getDeliveryClassEditURL( $code );
	}


	/**
	 * @return Delivery_Kind[]
	 */
	public function getKinds() : iterable
	{
		$kinds = [];
		foreach($this->getDeliveryMethods() as $method) {
			$kind = $method->getKind();

			$kinds[$kind->getCode()] = $kind;
		}

		return $kinds;
	}

	public function hasKind( string $kind ) : bool
	{
		return isset($this->getKinds()[$kind]);
	}

	public function isPersonalTakeOverOnly() : bool
	{
		foreach( $this->getKinds() as $code=>$kind ) {
			if($code!=Delivery_Kind::KIND_PERSONAL_TAKEOVER) {
				return false;
			}
		}

		return true;
	}

	public function isEDelivery() : bool
	{
		foreach( $this->getKinds() as $code=>$kind ) {
			if($code!=Delivery_Kind::KIND_E_DELIVERY) {
				return false;
			}
		}

		return true;
	}




	public static function getScope() : array
	{
		if(static::$scope===null) {
			$list = Delivery_Class::getList();

			static::$scope = [];

			foreach($list as $item) {
				static::$scope[$item->getCode()] = $item->getInternalName();
			}
		}

		return static::$scope;
	}


	/**
	 * @param array $codes
	 */
	public function setDeliveryMethods( array $codes ) : void
	{
		foreach($this->delivery_methods as $r) {
			if(!in_array($r->getMethodCode(), $codes)) {
				$r->delete();
				unset($this->delivery_methods[$r->getMethodCode()]);
			}
		}

		foreach( $codes as $code ) {
			if( !($r = Delivery_Method::get( $code )) ) {
				continue;
			}

			if(!isset($this->delivery_methods[$r->getCode()])) {
				$new_item = new Delivery_Class_Method();
				$new_item->setClassCode($this->getCode());
				$new_item->setMethodCode($code);

				$this->delivery_methods[$code] = $new_item;
				$new_item->save();
			}
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getDeliveryMethodCodes() : array
	{
		$codes = [];

		foreach($this->getDeliveryMethods() as $class) {
			$codes[] = $class->getCode();
		}

		return $codes;
	}

	/**
	 *
	 * @return Delivery_Method[]
	 */
	public function getDeliveryMethods() : iterable
	{
		$res = [];
		foreach($this->delivery_methods as $item) {
			$res[$item->getMethodCode()] = $item->getMethod();
		}

		return $res;
	}

}
