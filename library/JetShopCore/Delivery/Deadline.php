<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form;
use Jet\Application_Module;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Definition;
use Jet\Form_Field;

#[DataModel_Definition(
	name: 'delivery_deadline',
	database_table_name: 'delivery_deadlines',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Delivery_Deadline extends DataModel {
	protected static string $manage_module_name = 'Admin.Delivery.Deadlines';

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

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:'
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
	 * @var Delivery_Deadline_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Delivery_Deadline_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];


	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	/**
	 * @var Delivery_Deadline[]
	 */
	protected static array $loaded_items = [];

	public static function getManageModuleName() : string
	{
		return self::$manage_module_name;
	}

	public static function getManageModule() : Delivery_Deadline_ManageModuleInterface|Application_Module
	{
		return Application_Modules::moduleInstance( self::getManageModuleName() );
	}

	public function __construct()
	{
		parent::__construct();

		$this->afterLoad();
	}

	public function afterLoad() : void
	{
		Delivery_Deadline_ShopData::checkShopData( $this, $this->shop_data );
	}

	public static function get( string $id ) : Delivery_Deadline|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		static::$loaded_items[$id] = Delivery_Deadline::load( $id );

		return static::$loaded_items[$id];
	}


	/**
	 *
	 * @param string $search
	 *
	 * @return DataModel_Fetch_Instances|Delivery_Deadline[]
	 */
	public static function getList( string $search = '' ) : DataModel_Fetch_Instances|array
	{

		$where = [];
		if( $search ) {
			$search = '%'.$search.'%';

			$where[] = [
				'name *' => $search
			];
		}


		return static::fetchInstances( $where );
	}

	public static function getScope() : array
	{

		$scope = [];
		foreach(static::getList() as $i) {
			$scope[$i->getCode()] = $i->getInternalName();
		}

		return $scope;
	}


	public function getCode() : string
	{
		return $this->code;
	}

	public function setCode( string $code ) : void
	{
		$this->code = $code;
	}


	public function getInternalName() : string
	{
		return $this->internal_name;
	}

	public function setInternalName( string $internal_name ) : void
	{
		$this->internal_name = $internal_name;
	}


	public function getEditURL() : string
	{
		return Delivery_Deadline::getDeliveryTermEditURL( $this->code );
	}

	public static function getDeliveryTermEditURL( string $code ) : string
	{
		/**
		 * @var Delivery_Deadline_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Delivery_Deadline::getManageModuleName() );

		return $module->getDeliveryTermEditUrl( $code );
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			$this->_add_form->setCustomTranslatorDictionary( Delivery_Deadline::getManageModuleName() );
		}

		return $this->_add_form;
	}

	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}


	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			$this->_edit_form->setCustomTranslatorDictionary( Delivery_Deadline::getManageModuleName() );
		}

		return $this->_edit_form;
	}

	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}


	public function getShopData( ?Shops_Shop $shop=null ) : Delivery_Deadline_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

}