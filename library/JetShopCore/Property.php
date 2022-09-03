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
use Jet\DataModel_PropertyFilter;
use Jet\MVC;
use Jet\MVC_View;
use Jet\Tr;


#[DataModel_Definition(
	name: 'property',
	database_table_name: 'properties',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id']
)]
abstract class Core_Property extends DataModel {
	
	protected static string $manage_module_name = 'Admin.Catalog.Properties';
	
	const PROPERTY_TYPE_NUMBER = 'Number';
	const PROPERTY_TYPE_BOOL = 'Bool';
	const PROPERTY_TYPE_OPTIONS = 'Options';

	protected static array $types = [
		self::PROPERTY_TYPE_NUMBER => 'Number',
		self::PROPERTY_TYPE_BOOL => 'Yes / No',
		self::PROPERTY_TYPE_OPTIONS => 'Options',
	];

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $type = '';
	
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
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Decimal places:',
	)]
	protected int $decimal_places = 0;

	
	/**
	 * @var Property_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Property_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

	/**
	 * @var Property_Options_Option[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Property_Options_Option::class,

	)]
	protected array $options = [];

	protected Form|null $_add_form = null;

	protected Form|null $_edit_form = null;
	
	
	/**
	 * @var Property[]
	 */
	protected static array $loaded_items = [];
	

	public static function getTypes() : array
	{
		$list = [];

		foreach( self::$types as $type=>$label ) {
			$list[$type] = Tr::_($label, [], Category::getManageModuleName());
		}

		return $list;
	}
	
	public static function getManageModuleName() : string
	{
		return self::$manage_module_name;
	}
	
	public static function getManageModule() : Property_ManageModuleInterface|Application_Module
	{
		return Application_Modules::moduleInstance( Property::getManageModuleName() );
	}
	
	public function __construct() {
		parent::__construct();

		$this->afterLoad();
	}
	
	public function afterAdd(): void
	{
		/**
		 * @var Property $this
		 */
		Fulltext_Index_Internal_Property::addIndex( $this );
	}
	
	public function afterUpdate(): void
	{
		/**
		 * @var Property $this
		 */
		Fulltext_Index_Internal_Property::updateIndex( $this );
	}
	
	public function afterDelete(): void
	{
		/**
		 * @var Property $this
		 */
		Fulltext_Index_Internal_Property::deleteIndex( $this );
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
	
	
	public static function initByData( array $this_data, array $related_data = [], DataModel_PropertyFilter $load_filter = null ): static
	{
		if(static::class!=Property::class) {
			return parent::initByData( $this_data, $related_data, $load_filter );
		}
		
		/**
		 * @var DataModel $class_name
		 */
		$class_name = static::class.'_'.$this_data['type'];
		
		/**
		 * @var Property $item
		 */
		$item = $class_name::initByData( $this_data, $related_data, $load_filter );
		
		return $item;
	}


	public function afterLoad() : void
	{
		Property_ShopData::checkShopData($this, $this->shop_data);
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

	public function getType() : string
	{
		return $this->type;
	}

	public function getTypeTitle() : string
	{
		return Property::getTypes()[$this->getType()];
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

	
	
	public function getDecimalPlaces() : int
	{
		return $this->decimal_places;
	}

	public function setDecimalPlaces( int $decimal_places ) : void
	{
		$this->decimal_places = $decimal_places;
	}



	public function getShopData( ?Shops_Shop $shop=null ) : Property_ShopData
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


	/**
	 * @return Property_Options_Option[]
	 */
	public function getOptions() : iterable
	{
		return $this->options;
	}
	
	public function sortOptions( array $sort ) : void
	{
		$i = 0;
		foreach($sort as $id) {
			$i++;
			$this->options[$id]->setPriority($i);
		}
	}

	public function addOption( Property_Options_Option $option ) : void
	{
		$this->options[] = $option;
	}

	abstract public function getValueInstance() : Property_Value|null;
	
	abstract public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Properties_Property|null;





	public function getLabel( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getLabel();
	}

	public function getDescription( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getDescription();
	}

	public function getBoolYesDescription( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getBoolYesDescription();
	}

	public function getUrlParam( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getUrlParam();
	}

	public function getUnits( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getUnits();
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
		return Property::getPropertyEditURL( $this->id );
	}
	
	public static function getPropertyEditURL( int $id ) : string
	{
		return static::getManageModule()->getPropertyEditURL( $id );
	}
	
	public static function renderSelectPropertyWidget( string $on_select,
	                                                  int $selected_property_id=0,
	                                                  string $only_type='',
	                                                  bool $only_active=false,
	                                                  string $name='select_property' ) : string
	{
		$view = new MVC_View( MVC::getBase()->getViewsPath() );
		
		$view->setVar('selected_property_id', $selected_property_id);
		$view->setVar('only_type', $only_type);
		$view->setVar('on_select', $on_select);
		$view->setVar('name', $name);
		$view->setVar('only_active', $only_active);
		
		return $view->render('select-property-widget');
	}
	
	public function isItPossibleToDelete( array|null &$used_in_kinds_of_product=[] ) : bool
	{
		/**
		 * @var Property $this
		 */
		$used_in_kinds_of_product = KindOfProduct::getByProperty( $this );
		
		return count($used_in_kinds_of_product)==0;
	}
	
}