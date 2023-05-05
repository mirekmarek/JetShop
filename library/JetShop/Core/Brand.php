<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Application_Module;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Definition;
use Jet\Form_Field;

use JetApplication\Brand;
use JetApplication\Brand_ShopData;
use JetApplication\Brand_ManageModuleInterface;
use JetApplication\Product;
use JetApplication\Product_Category;
use JetApplication\Category;
use JetApplication\Cache;
use JetApplication\Shops;
use JetApplication\Shops_Shop;

#[DataModel_Definition(
	name: 'brands',
	database_table_name: 'brands',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Brand extends DataModel {

	protected static string $manage_module_name = 'Admin.Catalog.Brands';

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
		label: 'Name:'
	)]
	protected string $name = '';

	/**
	 * @var Brand_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Brand_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

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
		return self::$manage_module_name;
	}

	public static function getManageModule() : Brand_ManageModuleInterface|Application_Module
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
		Brand_ShopData::checkShopData( $this, $this->shop_data );
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

	public function getShopData( ?Shops_Shop $shop=null ) : Brand_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
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
			$this->_add_form = $this->createForm('add_form');
			$this->_add_form->setCustomTranslatorDictionary( Brand::getManageModuleName() );
		}

		return $this->_add_form;
	}

	public function catchAddForm() : bool
	{
		$add_form = $this->getAddForm();
		return $add_form->catch();
	}


	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->createForm('edit_form');
			$this->_edit_form->setCustomTranslatorDictionary( Brand::getManageModuleName() );
		}

		return $this->_edit_form;
	}

	public function catchEditForm() : bool
	{
		$edit_form = $this->getEditForm();
		return $edit_form->catch();
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
		$products = Product::dataFetchCol(select:['id'], where:[ 'brand_id'=>$this->id ]);
		if($products) {
			$categories = Product_Category::dataFetchCol(select:['category_id'], where:['product_id'=>$products] );


			foreach($categories as $id) {
				Category::addSyncCategory( $id );
			}

			Category::syncCategories();
		}
	}

}