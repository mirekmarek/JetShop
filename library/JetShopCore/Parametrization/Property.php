<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\DataModel_PropertyFilter;
use Jet\DataModel_Definition_Model_Related_1toN;
use Jet\DataModel_Related_1toN_Iterator;
use Jet\Tr;


#[DataModel_Definition(
	name: 'parametrization_properties',
	database_table_name: 'parametrization_properties',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Category::class,
	default_order_by: ['priority']
)]
abstract class Core_Parametrization_Property extends DataModel_Related_1toN {

	const PROPERTY_TYPE_NUMBER = 'Number';
	const PROPERTY_TYPE_BOOL = 'Bool';
	const PROPERTY_TYPE_OPTIONS = 'Options';
	const PROPERTY_TYPE_STENCIL_OPTIONS = 'StencilOptions';

	const SEO_STRATEGY_ADD_BEFORE = 'add_before';
	const SEO_STRATEGY_ADD_AFTER = 'add_after';
	const SEO_STRATEGY_DO_NOT_ADD = 'do_not_add';
	const SEO_STRATEGY_REPLACE = 'replace';

	protected static array $types = [
		self::PROPERTY_TYPE_NUMBER => 'Number',
		self::PROPERTY_TYPE_BOOL => 'Yes / No',
		self::PROPERTY_TYPE_OPTIONS => 'Options',
		self::PROPERTY_TYPE_STENCIL_OPTIONS => 'Stencil options',
	];

	#[DataModel_Definition(
		type: DataModel::TYPE_ID_AUTOINCREMENT,
		is_id: true,
		form_field_type: false
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false,
		related_to: 'main.id'
	)]
	protected int $category_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $group_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $priority = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type: false,
		max_len: 100
	)]
	protected string $type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Is variant selector'
	)]
	protected bool $is_variant_selector = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Is active'
	)]
	protected bool $is_active = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		default_value: true,
		form_field_label: 'Allow to show on a product detail'
	)]
	protected bool $allow_display = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		default_value: true,
		form_field_label: 'Allow to filter products'
	)]
	protected bool $allow_filter = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		default_value: true,
		form_field_label: 'Allow to compare products'
	)]
	protected bool $allow_compare = true;

	/**
	 * @var Parametrization_Property_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Property_ShopData::class
	)]
	protected $shop_data;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_label: 'Stencil:',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [ Stencil::class, 'getScope']
	)]
	protected int $stencil_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		form_field_label: 'Decimal places:'
	)]
	protected int $decimal_places = 0;

	/**
	 * @var Parametrization_Property_Option[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Property_Option::class,
		form_field_type: false

	)]
	protected $options;

	protected bool $is_inherited = false;

	protected bool $is_first = false;

	protected bool $is_last = false;

	protected Category|null $category = null;

	protected Parametrization_Group|null $group = null;

	protected Form|null $_add_form = null;

	protected Form|null $_edit_form = null;

	public static function getTypes() : array
	{
		$list = [];

		foreach( self::$types as $type=>$label ) {
			$list[$type] = Tr::_($label, [], Category::getManageModuleName());
		}

		return $list;
	}

	public function __construct() {
		parent::__construct();

		$this->afterLoad();
	}

	public static function initRelatedByData( array $this_data, array &$related_data, DataModel_PropertyFilter $load_filter = null ) : DataModel_Related_1toN_Iterator
	{

		/**
		 * @var DataModel_Definition_Model_Related_1toN $data_model_definition
		 */
		$data_model_definition = static::getDataModelDefinition();

		$items = [];

		foreach( $this_data as $d ) {
			/**
			 * @var DataModel $class_name
			 */
			$class_name = get_called_class().'_'.$d['type'];

			/**
			 * @var Parametrization_Property $item
			 */
			$item = $class_name::initByData( $d, $related_data, $load_filter );

			$items[] = $item;
		}


		/**
		 * @var DataModel_Related_1toN_Iterator $iterator
		 */

		$iterator_class_name = $data_model_definition->getIteratorClassName();

		$iterator = new $iterator_class_name( $data_model_definition, $items );

		return $iterator;
	}


	public function afterLoad() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_id = $shop->getId();

			if(!isset($this->shop_data[$shop_id])) {

				$sh = new Parametrization_Property_ShopData();
				$sh->setShopId($shop_id);

				$this->shop_data[$shop_id] = $sh;
			}
		}
	}

	public function getArrayKeyValue() : int|string|null
	{
		return $this->id;
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id )
	{
		$this->id = $id;
	}


	/**
	 * @param Category $category
	 * @param Parametrization_Group $group
	 */
	public function setParents( Category $category, Parametrization_Group $group )
	{

		$this->category = $category;
		$this->category_id = $category->getId();

		$this->group = $group;
		$this->group_id = $group->getId();

		foreach($this->shop_data as $shop_data) {
			/** @noinspection PhpParamsInspection */
			$shop_data->setParents( $category, $group, $this );
		}

		foreach($this->options as $option) {
			/** @noinspection PhpParamsInspection */
			$option->setParents( $category, $group, $this );
		}
	}

	public function getCategoryId() : int
	{
		return $this->category_id;
	}

	public function setCategoryId( int $category_id ) : void
	{
		$this->category_id = $category_id;
	}

	public function setGroupId( int $group_id ) : void
	{
		$this->group_id = $group_id;
	}

	public function getGroupId() : int
	{
		return $this->group_id;
	}

	public function getCategory() : Category
	{
		return $this->category;
	}

	public function getGroup() : Parametrization_Group
	{
		return $this->group;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function getIsActive() : bool
	{
		return $this->is_active;
	}

	public function setPriority( int $priority ) : void
	{
		$this->priority = $priority;
	}

	public function getPriority() : int
	{
		return $this->priority;
	}

	public function setAllowDisplay( bool $display_it) : void
	{
		$this->allow_display = $display_it;
	}

	public function getAllowDisplay() : bool
	{
		return $this->allow_display;
	}

	public function setAllowFilter( bool $filter_by ) : void
	{
		$this->allow_filter = $filter_by;
	}

	public function getAllowFilter() : bool
	{
		return $this->allow_filter;
	}

	public function getIsFilterable() : bool
	{
		if (!$this->getIsActive() || !$this->getAllowFilter()) {
			return false;
		}

		return true;
	}

	public function setAllowCompare( bool $compare ) : void
	{
		$this->allow_compare = $compare;
	}

	public function getAllowCompare() : bool
	{
		return $this->allow_compare;
	}

	public function isInherited() : bool
	{
		return $this->is_inherited;
	}

	public function setIsInherited( bool $is_inherited ) : void
	{
		$this->is_inherited = $is_inherited;
	}

	public function setIsFirst( bool $is_first) : void
	{
		$this->is_first = $is_first;
	}

	public function getIsFirst() : bool
	{
		return $this->is_first;
	}

	public function setIsLast( bool $is_last ) : void
	{
		$this->is_last = $is_last;
	}

	public function getIsLast() : bool
	{
		return $this->is_last;
	}

	public function getType() : string
	{
		return $this->type;
	}

	public function getTypeTitle() : string
	{
		return Parametrization_Property::getTypes()[$this->getType()];
	}

	public function isVariantSelector() : bool
	{
		return $this->is_variant_selector;
	}

	public function setIsVariantSelector( bool $is_variant_selector ) : void
	{
		$this->is_variant_selector = $is_variant_selector;
	}

	public function getStencilId() : int
	{
		return $this->stencil_id;
	}

	public function setStencilId( int $stencil_id ) : void
	{
		$this->stencil_id = $stencil_id;
	}

	public function getDecimalPlaces() : int
	{
		return $this->decimal_places;
	}

	public function setDecimalPlaces( int $decimal_places ) : void
	{
		$this->decimal_places = $decimal_places;
	}


	public function getShopData( string|null $shop_id=null ) : Parametrization_Property_ShopData
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return $this->shop_data[$shop_id];
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
		$form = $this->getCommonForm('property_add_form');

		foreach( Shops::getList() as $shop ) {
			$shop_id = $shop->getId();

			$seo_h1_strategy = $form->field('/shop_data/'.$shop_id.'/seo_h1_strategy');
			$seo_h1_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_title_strategy = $form->field('/shop_data/'.$shop_id.'/seo_title_strategy');
			$seo_title_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_description_strategy = $form->field('/shop_data/'.$shop_id.'/seo_description_strategy');
			$seo_description_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);


		}

		return $form;
	}

	public function catchAddForm() : bool
	{
		$form = $this->getAddForm();

		if(!$form->catchInput() || !$form->validate()) {
			return false;
		}

		$form->catchData();

		return true;
	}

	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->generateEditForm();
			if($this->isInherited()) {
				$this->_edit_form->setIsReadonly();
			}
		}

		return $this->_edit_form;
	}

	protected function generateEditForm() : Form
	{
		$form = $this->getCommonForm('property_edit_form_'.$this->id);


		foreach( Shops::getList() as $shop ) {
			$shop_id = $shop->getId();

			$seo_h1_strategy = $form->field('/shop_data/'.$shop_id.'/seo_h1_strategy');
			$seo_h1_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_title_strategy = $form->field('/shop_data/'.$shop_id.'/seo_title_strategy');
			$seo_title_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_description_strategy = $form->field('/shop_data/'.$shop_id.'/seo_description_strategy');
			$seo_description_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);


		}

		return $form;
	}

	public function catchEditForm() : bool
	{
		$form = $this->getEditForm();

		if(!$form->catchInput() || !$form->validate()) {
			return false;
		}

		$form->catchData();

		return true;
	}

	public function getOption( int $id ) : Parametrization_Property_Option|Stencil_Option|null
	{
		if(!isset($this->options[$id])) {
			return null;
		}

		$option = $this->options[$id];

		/** @noinspection PhpParamsInspection */
		$option->setParents( $this->category, $this->group, $this );

		return $option;
	}


	/**
	 * @return Parametrization_Property_Option[]
	 */
	public function getOptions() : iterable
	{
		foreach($this->options as $option) {
			/** @noinspection PhpParamsInspection */
			$option->setParents( $this->category, $this->group, $this );
		}

		return $this->options;
	}

	public function addOption( Parametrization_Property_Option $option ) : void
	{

		/** @noinspection PhpParamsInspection */
		$option->setParents( $this->category, $this->group, $this );

		$this->options[] = $option;
	}

	abstract public function getValueInstance() : Parametrization_Property_Value|null;
	
	abstract public function getFilterInstance( ProductListing $listing ) : ProductListing_Filter_Properties_Property|null;





	public function getLabel( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getLabel();
	}

	public function getDescription( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getDescription();
	}

	public function getBoolYesDescription( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getBoolYesDescription();
	}

	public function getUrlParam( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getUrlParam();
	}

	public function getUnits( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getUnits();
	}

	public function getSeoH1( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getUnits();
	}

	public function getSeoH1Strategy( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getSeoH1Strategy();
	}

	public function getSeoTitle( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getSeoTitle();
	}

	public function getSeoTitleStrategy( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getSeoTitleStrategy();
	}

	public function getSeoDescription( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getSeoDescription();
	}

	public function getSeoDescriptionStrategy( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getSeoDescriptionStrategy();
	}

	public function getImageMain( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImageMain();
	}

	public function getImageMainUrl( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImageMainUrl();
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h, string|null $shop_id=null  ) : string
	{
		return $this->getShopData($shop_id)->getImageMainThumbnailUrl( $max_w, $max_h );
	}

	public function getImagePictogram( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImagePictogram();
	}

	public function getImagePictogramUrl( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImagePictogramUrl();
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, string|null $shop_id=null  ) : string
	{
		return $this->getShopData($shop_id)->getImagePictogramThumbnailUrl( $max_w, $max_h );
	}

}