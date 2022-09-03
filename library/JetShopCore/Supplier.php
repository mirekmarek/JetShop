<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Definition;
use Jet\Form_Field;

#[DataModel_Definition(
	name: 'suppliers',
	database_table_name: 'suppliers',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Supplier extends DataModel {
	protected static string $manage_module_name = 'Admin.Catalog.Suppliers';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	protected string $name = '';

	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	/**
	 * @var Supplier[]
	 */
	protected static array $loaded_items = [];

	public static function getManageModuleName() : string
	{
		return self::$manage_module_name;
	}

	public static function getManageModule() : Supplier_ManageModuleInterface|Application_Module
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
	}

	public static function get( int $id ) : Supplier|null 
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		static::$loaded_items[$id] = Supplier::load( $id );

		return static::$loaded_items[$id];
	}


	/**
	 *
	 * @param string $search
	 *
	 * @return DataModel_Fetch_Instances|Supplier[]
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


		$list = static::fetchInstances( $where );

		$list->getQuery()->setOrderBy( 'name' );

		return $list;
	}

	public static function getScope() : array
	{

		$scope = [];
		foreach(static::getList() as $i) {
			$scope[$i->getId()] = $i->getName();
		}

		return $scope;
	}


	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getName() : string
	{
		return $this->name;
	}

	public function setName( string $name ) : void
	{
		$this->name = $name;
	}


	public function getEditURL() : string
	{
		return Supplier::getSupplierEditURL( $this->id );
	}

	public static function getSupplierEditURL( int $id ) : string
	{
		/**
		 * @var Supplier_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Supplier::getManageModuleName() );

		return $module->getSupplierEditUrl( $id );
	}


	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			$this->_add_form->setCustomTranslatorDictionary( Supplier::getManageModuleName() );
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
			$this->_edit_form->setCustomTranslatorDictionary( Supplier::getManageModuleName() );
		}

		return $this->_edit_form;
	}

	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}
	

	public function afterAdd() : void
	{
	}

	public function afterUpdate() : void
	{
	}

	public function afterDelete() : void
	{
	}

}