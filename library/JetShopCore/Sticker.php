<?php
namespace JetShop;

use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form;
use Jet\DataModel_Fetch_Instances;
use Jet\Form_Field_Input;
use Jet\Tr;

#[DataModel_Definition(
	name: 'stickers',
	database_table_name: 'stickers',
	id_controller_class: DataModel_IDController_Passive::class,
)]
abstract class Core_Sticker extends DataModel {
	protected static string $manage_module_name = 'Admin.Catalog.Stickers';

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
	 * @var Sticker_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Sticker_ShopData::class
	)]
	protected array $shop_data = [];

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
		return self::$manage_module_name;
	}

	public static function getManageModule() : Sticker_ManageModuleInterface|Application_Module
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
		Sticker_ShopData::checkShopData( $this, $this->shop_data );
	}

	public static function get( string $code ) : Sticker|null
	{
		if(isset( static::$loaded_items[$code])) {
			return static::$loaded_items[$code];
		}

		static::$loaded_items[$code] = Sticker::load( $code );

		return static::$loaded_items[$code];
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
					static::$_all[$sticker->getCode()] = $sticker;
					static::$loaded_items[$sticker->getCode()] = $sticker;
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
				'code *' => $search,
				'OR',
				'internal_name *' => $search
			];
		}


		$list = static::fetchInstances( $where );

		$list->getQuery()->setOrderBy( 'internal_name' );

		return $list;
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

	public function getShopData( ?Shops_Shop $shop=null ) : Sticker_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}


	public function getEditURL() : string
	{
		return Sticker::getStickerEditURL( $this->code );
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

			$code = $this->_add_form->getField('code');

			$code->setValidator(function( Form_Field_Input $field ) {
				$value = $field->getValue();
				if(!$value) {
					$field->setError( Form_Field_Input::ERROR_CODE_EMPTY );
					return false;
				}

				$exists = Sticker::get($value);

				if($exists) {
					$field->setCustomError(
						Tr::_('Sticker with the same name already exists')
					);

					return false;
				}

				return true;
			});

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
			$this->_edit_form->getField('code')->setIsReadonly(true);
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
		$products = Product_Sticker::fetchData(['product_id'], [ 'sticker_code'=>$this->code ], '', 'fetchCol');

		if($products) {
			$categories = Product_Category::fetchData(['category_id'], ['product_id'=>$products], '', 'fetchCol' );


			foreach($categories as $id) {
				Category::addSyncCategory( $id );
			}

			Category::syncCategories();
		}

	}
}