<?php
namespace JetShop;

use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Fetch_Instances;

#[DataModel_Definition(
	name: 'stickers',
	database_table_name: 'stickers',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Sticker extends DataModel {
	protected static string $MANAGE_MODULE = 'Admin.Catalog.Stickers';

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
	 * @var Sticker_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Sticker_ShopData::class
	)]
	protected $shop_data;

	/**
	 * @var Sticker[]
	 */
	protected static array|null $_all = null;

	protected ?Form $_add_form = null;

	protected ?Form $_edit_form = null;

	/**
	 * @var Sticker[]
	 */
	protected static array $loaded_items = [];

	public static function getManageModuleName() : string
	{
		return self::$MANAGE_MODULE;
	}

	public static function getManageModule() : Sticker_ManageModuleInterface|Application_Module
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
			$shop_code = $shop->getCode();

			if(!isset($this->shop_data[$shop_code])) {

				$sh = new Sticker_ShopData();
				$sh->setShopCode($shop_code);

				$this->shop_data[$shop_code] = $sh;
			}

			/** @noinspection PhpParamsInspection */
			$this->shop_data[$shop_code]->setParents( $this );
		}
	}

	public static function get( int $id ) : Sticker|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}

		static::$loaded_items[$id] = Sticker::load( $id );

		return static::$loaded_items[$id];
	}

	/**
	 * @return Sticker[]
	 */
	public static function getAll() : array
	{
		if( static::$_all===null )
		{
			$all = Cache::load( 'stickers' );

			if(!$all) {
				$list = static::fetchInstances();

				$list->getQuery()->setOrderBy( 'name' );

				static::$_all = [];

				foreach($list as $sticker) {
					/**
					 * @var Sticker $sticker
					 */
					static::$_all[$sticker->getId()] = $sticker;
					static::$loaded_items[$sticker->getId()] = $sticker;
				}

				Cache::save('stickers', static::$_all);
			} else {
				static::$_all = $all;

				foreach(static::$_all as $id=>$sticker) {
					static::$loaded_items[$id] = $sticker;
				}
			}
		}

		return static::$_all;
	}


	/**
	 * @param string $search
	 *
	 * @return DataModel_Fetch_Instances|Sticker[]
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

	public function getShopData( string|null $shop_code=null ) : Sticker_ShopData|null
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return $this->shop_data[$shop_code];
	}

	public function getEditURL() : string
	{
		return Sticker::getStickerEditURL( $this->id );
	}

	public static function getStickerEditURL( int $id ) : string
	{
		/**
		 * @var Sticker_ManageModuleInterface $module
		 */
		$module = Application_Modules::moduleInstance( Sticker::getManageModuleName() );

		return $module->getStickerEditUrl( $id );
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->getCommonForm('add_form');
			$this->_add_form->setCustomTranslatorNamespace( Sticker::getManageModuleName() );
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
			$this->_edit_form->setCustomTranslatorNamespace( Sticker::getManageModuleName() );
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
		$products = Product_Sticker::fetchData(['product_id'], [ 'sticker_id'=>$this->id ], '', 'fetchCol');

		if($products) {
			$categories = Product_Category::fetchData(['category_id'], ['product_id'=>$products], '', 'fetchCol' );


			foreach($categories as $id) {
				Category::addSyncCategory( $id );
			}

			Category::syncCategories();
		}

	}
}