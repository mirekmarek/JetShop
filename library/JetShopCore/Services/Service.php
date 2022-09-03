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
use Jet\Form_Field;
use Jet\Form_Definition;
use Jet\Form_Field_Input;
use Jet\Form_Field_Select;

/**
 *
 */
#[DataModel_Definition(
	name: 'service',
	database_table_name: 'services',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Services_Service extends DataModel
{
	protected static string $manage_module_name = 'Admin.Services.Services';

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
			Form_Field::ERROR_CODE_EMPTY => 'Please enter code',
			'code_used' => 'Service with the same name already exists',
		]
	)]
	protected string $code = '';

	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		is_key: true,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Group:'
	)]
	protected string $group = '';

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
		select_options_creator: [Services_Kind::class, 'getScope'],
		error_messages: [
			Form_Field_Select::ERROR_CODE_EMPTY => 'Please select kind',
			Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select kind'
		]
	)]
	protected string $kind = '';


	/**
	 * @var Services_Service_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Services_Service_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

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
		return self::$manage_module_name;
	}

	/**
	 * @param string $name
	 */
	public static function setManageModuleName( string $name ): void
	{
		self::$manage_module_name = $name;
	}

	public function __construct()
	{
		parent::__construct();

		$this->afterLoad();
	}

	public function afterLoad() : void
	{
		Services_Service_ShopData::checkShopData( $this, $this->shop_data );
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

			$code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if(!$value) {
					$field->setError( Form_Field::ERROR_CODE_EMPTY );
					return false;
				}

				$exists = Services_Service::get($value);

				if($exists) {
					$field->setError( 'code_used' );

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
	 * @return static[]
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
	 * @return string
	 */
	public function getKindCode(): string
	{
		return $this->kind;
	}

	/**
	 * @param string $kind
	 */
	public function setKindCode( string $kind ): void
	{
		$this->kind = $kind;
	}


	public function getKind(): ?Services_Kind
	{
		return Services_Kind::get( $this->kind );
	}

	public function getKindTitle() : string
	{
		$kind = $this->getKind();
		return $kind?$kind->getTitle() : '?';
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

	public function getShopData( ?Shops_Shop $shop=null ) : Services_Service_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	public function getEditURL() : string
	{
		return Services_Service::getServiceEditURL( $this->getCode() );
	}

	public static function getServiceEditURL( string $code ) : string
	{
		/**
		 * @var Services_Service_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Sticker::getManageModuleName() );

		return $module->getServiceEditURL( $code );
	}

	/**
	 * @param string $value
	 */
	public function setGroup( string $value ) : void
	{
		$this->group = $value;
	}

	/**
	 * @return string
	 */
	public function getGroup() : string
	{
		return $this->group;
	}

	public static function getScope( string $kind ) : array
	{
		if(static::$scope===null) {
			$list = Services_Service::getList();

			static::$scope = [];

			foreach(Services_Kind::getScope() as $_kind=>$kind_title) {
				static::$scope[$_kind] = [];
			}

			foreach($list as $item) {
				static::$scope[$item->getKindCode()][$item->getCode()] = $item->getInternalName();
			}
		}

		return static::$scope[$kind];
	}
	
	public static function getDeliveryServicesScope() : array
	{
		return static::getScope( Services_Kind::KIND_DELIVERY );
	}
	
	public static function getPaymentServicesScope() : array
	{
		return static::getScope( Services_Kind::KIND_PAYMENT );
	}
	
	public static function getOtherServicesScope() : array
	{
		return static::getScope( Services_Kind::KIND_OTHER );
	}
	
}
