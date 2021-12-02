<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Application_Module;
use Jet\DataModel_Fetch_Instances;

#[DataModel_Definition(
	name: 'stencils',
	database_table_name: 'stencils',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Stencil extends DataModel {

	protected static string $manage_module_name = 'Admin.Catalog.Stencils';

	const IMG_MAIN = 'main';
	const IMG_PICTOGRAM = 'pictogram';

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
		form_field_type: false		
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		form_field_label: 'Name:'		
	)]
	protected string $name = '';

	/**
	 * @var Stencil_Option[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Stencil_Option::class,
		form_field_type: false
	)]
	protected array $options = [];

	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	/**
	 * @var Stencil[]
	 */
	protected static array $loaded_items = [];

	public static function getManageModuleName() : string
	{
		return self::$manage_module_name;
	}

	public static function getManageModule() : Stencil_ManageModuleInterface|Application_Module
	{
		return Application_Modules::moduleInstance( self::getManageModuleName() );
	}

	public function __construct()
	{
		parent::__construct();

		$this->afterLoad();
	}

	public static function get( int $id ) : Stencil|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		static::$loaded_items[$id] = Stencil::load( $id );

		return static::$loaded_items[$id];
	}


	/**
	 *
	 * @param string $search
	 *
	 * @return DataModel_Fetch_Instances|Stencil[]
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


		$list = static::fetchInstances(
			$where,
			[
				'id',
				'name'

			]);

		$list->getQuery()->setOrderBy( 'name' );

		return $list;
	}

	public static function getScope() : array
	{
		$result = [];

		foreach(Stencil::getList() as $stc) {
			$result[$stc->getId()] = $stc->getName();
		}

		return $result;
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
		return Stencil::getStencilEditURL( $this->id );
	}

	public static function getStencilEditURL( int $id ) : string
	{
		/**
		 * @var Stencil_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Stencil::getManageModuleName() );

		return $module->getStencilEditUrl( $id );
	}



	/**
	 * @return Stencil_Option[]
	 */
	public function getOptions() : iterable
	{
		return $this->options;
	}


	public function getOption( int $id ) : Stencil_Option|null
	{
		if(!isset($this->options[$id])) {
			return null;
		}

		return $this->options[$id];
	}

	public function addOption( Stencil_Option $option ) : void
	{
		$this->options[] = $option;
	}


	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->getCommonForm('add_form');
			$this->_add_form->setCustomTranslatorNamespace( Stencil::getManageModuleName() );
		}

		return $this->_add_form;
	}

	public function catchAddForm() : bool
	{
		$add_form = $this->getAddForm();
		if(
			!$add_form->catchInput() ||
			!$add_form->validate()
		) {
			return false;
		}

		$add_form->catchData();

		return true;
	}



	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->getCommonForm('edit_form');
			$this->_edit_form->setCustomTranslatorNamespace( Stencil::getManageModuleName() );
		}

		return $this->_edit_form;
	}


	public function catchEditForm() : bool
	{
		$edit_form = $this->getEditForm();
		if(
			!$edit_form->catchInput() ||
			!$edit_form->validate()
		) {
			return false;
		}

		$edit_form->catchData();

		return true;
	}

	
	public function afterAdd() : void
	{
	}

	public function afterUpdate() : void
	{
		$this->actualizeReferences();
	}

	public function afterDelete() : void
	{
		$this->actualizeReferences();
	}

	public function actualizeReferences() : void
	{

		$categories = Parametrization_Property::fetchData(['category_id'], ['stencil_id'=>$this->id], null, 'fetchCol');

		do {
			$ref_found = false;

			foreach($categories as $id) {
				$_references = Category::fetchData(['id'], [
					'parameter_inherited_category_id' => $id,
					'OR',
					[
						'parameter_strategy' => Category::PARAMETER_STRATEGY_INHERITED_FROM_PARENT,
						'AND',
						'parent_id' => $id
					]
				], null, 'fetchCol');

				foreach($_references as $r_id) {
					if(!in_array($r_id, $categories)) {
						$categories[] = $r_id;

						$ref_found = true;
					}
				}
			}
		} while( $ref_found );

		foreach($categories as $id) {
			Category::addSyncCategory( $id );
		}

		Category::syncCategories();
	}

}