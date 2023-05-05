<?php
namespace JetShop;
use Jet\Application_Module;
use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\MVC;
use Jet\MVC_View;

use JetApplication\PropertyGroup_ShopData;
use JetApplication\PropertyGroup;
use JetApplication\PropertyGroup_ManageModuleInterface;
use JetApplication\Fulltext_Index_Internal_PropertyGroup;
use JetApplication\Shops_Shop;
use JetApplication\KindOfProduct;
use JetApplication\Property_Options_Option;
use JetApplication\Shops;

#[DataModel_Definition(
	name: 'property_group',
	database_table_name: 'property_groups',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_PropertyGroup extends DataModel {
	
	protected static string $manage_module_name = 'Admin.Catalog.PropertyGroups';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Internal name:'
	)]
	protected string $internal_name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Internal notes:'
	)]
	protected string $internal_notes = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active'
	)]
	protected bool $is_active = true;

	
	/**
	 * @var PropertyGroup_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: PropertyGroup_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

	protected Form|null $_add_form = null;

	protected Form|null $_edit_form = null;
	
	
	/**
	 * @var PropertyGroup[]
	 */
	protected static array $loaded_items = [];
	
	
	public static function getManageModuleName() : string
	{
		return self::$manage_module_name;
	}
	
	public static function getManageModule() : PropertyGroup_ManageModuleInterface|Application_Module
	{
		return Application_Modules::moduleInstance( PropertyGroup::getManageModuleName() );
	}

	
	public function __construct() {
		parent::__construct();

		$this->afterLoad();
	}
	
	public function afterAdd(): void
	{
		/**
		 * @var PropertyGroup $this
		 */
		Fulltext_Index_Internal_PropertyGroup::addIndex( $this );
	}
	
	public function afterUpdate(): void
	{
		/**
		 * @var PropertyGroup $this
		 */
		Fulltext_Index_Internal_PropertyGroup::updateIndex( $this );
	}
	
	public function afterDelete(): void
	{
		/**
		 * @var PropertyGroup $this
		 */
		Fulltext_Index_Internal_PropertyGroup::deleteIndex( $this );
	}
	
	
	public static function get( int $id ) : static|null
	{
		if(isset(static::$loaded_items[$id])) {
			return static::$loaded_items[$id];
		}
		

		static::$loaded_items[$id] = static::load( $id );
		
		return static::$loaded_items[$id];
	}
	
	/**
	 * @return static[]
	 */
	public static function getList() : iterable
	{
		$where = [];
		
		return static::fetchInstances( $where );
	}

	public function afterLoad() : void
	{
		PropertyGroup_ShopData::checkShopData($this, $this->shop_data);
	}

	
	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id )
	{
		$this->id = $id;
	}
	
	
	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function isActive() : bool
	{
		return $this->is_active;
	}

	
	public function getInternalName(): string
	{
		return $this->internal_name;
	}
	
	public function setInternalName( string $internal_name ): void
	{
		$this->internal_name = $internal_name;
	}
	
	public function getInternalNotes(): string
	{
		return $this->internal_notes;
	}
	
	public function setInternalNotes( string $internal_notes ): void
	{
		$this->internal_notes = $internal_notes;
	}
	


	public function getShopData( ?Shops_Shop $shop=null ) : PropertyGroup_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->generateAddForm();
		}

		return $this->_add_form;
	}

	protected function generateAddForm() : Form
	{
		$form = $this->createForm('property_add_form');

		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
		}

		return $form;
	}

	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}

	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->generateEditForm();
		}

		return $this->_edit_form;
	}

	protected function generateEditForm() : Form
	{
		$form = $this->createForm('property_edit_form_'.$this->id);


		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
		}

		return $form;
	}

	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	public function getOption( int $id ) : Property_Options_Option|null
	{
		if(!isset($this->options[$id])) {
			return null;
		}

		return $this->options[$id];
	}


	public function getLabel( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getLabel();
	}

	public function getDescription( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getDescription();
	}
	
	public function getImageMain( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImageMain();
	}

	public function getImageMainUrl( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImageMainUrl();
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop=null   ) : string
	{
		return $this->getShopData($shop)->getImageMainThumbnailUrl( $max_w, $max_h );
	}

	public function getImagePictogram( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImagePictogram();
	}

	public function getImagePictogramUrl( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImagePictogramUrl();
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop=null   ) : string
	{
		return $this->getShopData($shop)->getImagePictogramThumbnailUrl( $max_w, $max_h );
	}
	
	
	public function getEditURL() : string
	{
		return PropertyGroup::getPropertyGroupEditURL( $this->id );
	}
	
	public static function getPropertyGroupEditURL( int $id ) : string
	{
		return static::getManageModule()->getPropertyGroupEditURL( $id );
	}
	
	public static function renderSelectPropertyGroupWidget( string $on_select,
	                                                   int $selected_property_group_id=0,
	                                                   bool $only_active=false,
	                                                   string $name='select_property_group' ) : string
	{
		$view = new MVC_View( MVC::getBase()->getViewsPath() );
		
		$view->setVar('selected_property_group_id', $selected_property_group_id);
		$view->setVar('on_select', $on_select);
		$view->setVar('name', $name);
		$view->setVar('only_active', $only_active);
		
		return $view->render('select-property-group-widget');
	}
	
	public function isItPossibleToDelete( array|null &$used_in_kinds_of_product=[] ) : bool
	{
		/**
		 * @var PropertyGroup $this
		 */
		$used_in_kinds_of_product = KindOfProduct::getByPropertyGroup( $this );
		
		return count($used_in_kinds_of_product)==0;
	}
	
}