<?php
/**
 * 
 */

namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Name;
use Jet\Form;
use Jet\Form_Field_Input;
use Jet\Tr;
use Jet\Form_Field_Select;

/**
 *
 */
#[DataModel_Definition(
	name: 'delivery_class',
	database_table_name: 'delivery_classes',
	id_controller_class: DataModel_IDController_Name::class,
	id_controller_options: [
		'id_property_name' => 'code',
		'get_name_method_name' => 'getCode'
	]
)]
abstract class Core_Delivery_Class extends DataModel
{

	protected static string $MANAGE_MODULE = 'Admin.Delivery.Classes';

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
		max_len: 99999,
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

				$exists = Delivery_Class::get($value);

				if($exists) {
					$field->setCustomError(
						Tr::_('Delivery class with the same name already exists')
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
	 * @param int|string $id
	 * @return static|null
	 */
	public static function get( int|string $id ) : static|null
	{
		return static::load( $id );
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
		$module = Application_Modules::moduleInstance( Sticker::getManageModuleName() );

		return $module->getDeliveryClassEditURL( $code );
	}

	/**
	 * @param string $value
	 */
	public function setKind( string $value ) : void
	{
		$this->kind = $value;
	}

	/**
	 * @return string
	 */
	public function getKind() : string
	{
		return $this->kind;
	}

	public static function getScope() : array
	{
		if(static::$scope===null) {
			$list = Delivery_Class::getList();

			static::$scope = [];

			foreach($list as $kind) {
				static::$scope[$kind->getCode()] = $kind->getInternalName();
			}
		}

		return static::$scope;
	}

}
