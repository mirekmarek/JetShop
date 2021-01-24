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
	name: 'brands',
	database_table_name: 'brands',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Brand extends DataModel {

	protected static string $MANAGE_MODULE = 'Admin.Catalog.Brands';

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
	 * @var Brand_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Brand_ShopData::class
	)]
	protected $shop_data;

	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	/**
	 * @var Brand[]
	 */
	protected static array $loaded_items = [];

	/**
	 * @var Brand[]
	 */
	protected static array|null $_all = null;

	public static function getManageModuleName() : string
	{
		return self::$MANAGE_MODULE;
	}

	public static function getManageModule() : Brand_ManageModuleInterface|Application_Module
	{
		/** @noinspection PhpIncompatibleReturnTypeInspection */
		return Application_Modules::moduleInstance( self::getManageModuleName() );
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

				$sh = new Brand_ShopData();
				$sh->setShopId($shop_id);

				$this->shop_data[$shop_id] = $sh;
			}

			/** @noinspection PhpParamsInspection */
			$this->shop_data[$shop_id]->setParents( $this );
		}
	}

	public static function get( int $id ) : Brand|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		static::$loaded_items[$id] = Brand::load( $id );

		return static::$loaded_items[$id];
	}

	/**
	 * @return Brand[]
	 */
	public static function getAll() : array
	{
		if( static::$_all===null )
		{
			$all = Cache::load( 'brands' );

			if(!$all) {
				$list = static::fetchInstances();

				$list->getQuery()->setOrderBy( 'name' );

				static::$_all = [];

				foreach($list as $brand) {
					/**
					 * @var Brand $brand
					 */
					static::$_all[$brand->getId()] = $brand;
					static::$loaded_items[$brand->getId()] = $brand;
				}

				Cache::save('brands', static::$_all);
			} else {
				static::$_all = $all;

				foreach(static::$_all as $id=>$brand) {
					static::$loaded_items[$id] = $brand;
				}
			}
		}

		return static::$_all;
	}

	/**
	 *
	 * @param string $search
	 *
	 * @return DataModel_Fetch_Instances|Brand[]
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

	public function getShopData( string|null $shop_id=null ) : Brand_ShopData
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return $this->shop_data[$shop_id];
	}

	public function getEditURL() : string
	{
		return Brand::getBrandEditURL( $this->id );
	}

	public static function getBrandEditURL( int $id ) : string
	{
		/**
		 * @var Brand_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Brand::getManageModuleName() );

		return $module->getBrandEditUrl( $id );
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->getCommonForm('add_form');
			$this->_add_form->setCustomTranslatorNamespace( Brand::getManageModuleName() );
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
			$this->_edit_form->setCustomTranslatorNamespace( Brand::getManageModuleName() );
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
		$products = Product::fetchData(['id'], [ 'brand_id'=>$this->id ], '', 'fetchCol');
		if($products) {
			$categories = Product_Category::fetchData(['category_id'], ['product_id'=>$products], '', 'fetchCol' );


			foreach($categories as $id) {
				Category::addSyncCategory( $id );
			}

			Category::syncCategories();
		}

	}

}