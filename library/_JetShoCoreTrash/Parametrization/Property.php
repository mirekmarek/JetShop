<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_Related_1toN;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\Form;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Form_Field_Select;
use Jet\DataModel_PropertyFilter;
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
	)]
	protected int $id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		related_to: 'main.id'
	)]
	protected int $category_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $group_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100
	)]
	protected string $type = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is variant selector'
	)]
	protected bool $is_variant_selector = false;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Is active'
	)]
	protected bool $is_active = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Allow to show on a product detail'
	)]
	protected bool $allow_display = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Allow to filter products'
	)]
	protected bool $allow_filter = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_CHECKBOX,
		label: 'Allow to compare products'
	)]
	protected bool $allow_compare = true;

	/**
	 * @var Parametrization_Property_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Property_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_SELECT,
		label: 'Stencil:',
		select_options_creator: [ Stencil::class, 'getScope']
	)]
	protected int $stencil_id = 0;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INT,
		label: 'Decimal places:',
	)]
	protected int $decimal_places = 0;

	/**
	 * @var Parametrization_Property_Option[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Property_Option::class,

	)]
	protected array $options = [];

	protected bool $is_inherited = false;

	protected bool $is_first = false;

	protected bool $is_last = false;

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

	public static function initRelatedByData( array $this_data, array &$related_data, DataModel_PropertyFilter $load_filter = null ) : array
	{

		$items = [];

		foreach( $this_data as $d ) {
			/**
			 * @var DataModel $class_name
			 */
			$class_name = static::class.'_'.$d['type'];

			/**
			 * @var Parametrization_Property $item
			 */
			$item = $class_name::initByData( $d, $related_data, $load_filter );

			$items[] = $item;
		}

		return $items;
	}


	public function afterLoad() : void
	{
		Parametrization_Property_ShopData::checkShopData($this, $this->shop_data);
	}

	public function getArrayKeyValue() : string
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
		return Category::get($this->category_id);
	}

	public function getGroup() : Parametrization_Group
	{
		return $this->getCategory()->getParametrizationGroup($this->group_id);
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



	public function getShopData( ?Shops_Shop $shop=null ) : Parametrization_Property_ShopData
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

			$seo_h1_strategy = $form->field('/shop_data/'.$shop_key.'/seo_h1_strategy');
			$seo_h1_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_title_strategy = $form->field('/shop_data/'.$shop_key.'/seo_title_strategy');
			$seo_title_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_description_strategy = $form->field('/shop_data/'.$shop_key.'/seo_description_strategy');
			$seo_description_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);


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
			if($this->isInherited()) {
				$this->_edit_form->setIsReadonly();
			}
		}

		return $this->_edit_form;
	}

	protected function generateEditForm() : Form
	{
		$form = $this->createForm('property_edit_form_'.$this->id);


		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();

			$seo_h1_strategy = $form->field('/shop_data/'.$shop_key.'/seo_h1_strategy');
			$seo_h1_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_title_strategy = $form->field('/shop_data/'.$shop_key.'/seo_title_strategy');
			$seo_title_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);

			$seo_description_strategy = $form->field('/shop_data/'.$shop_key.'/seo_description_strategy');
			$seo_description_strategy->setErrorMessages([
				Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
			]);


		}

		return $form;
	}

	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
	}

	public function getOption( int $id ) : Parametrization_Property_Option|Stencil_Option|null
	{
		if(!isset($this->options[$id])) {
			return null;
		}

		return $this->options[$id];
	}


	/**
	 * @return Parametrization_Property_Option[]
	 */
	public function getOptions() : iterable
	{
		return $this->options;
	}

	public function addOption( Parametrization_Property_Option $option ) : void
	{
		$this->options[] = $option;
	}

	abstract public function getValueInstance() : Parametrization_Property_Value|null;
	
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

	public function getSeoH1( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getUnits();
	}

	public function getSeoH1Strategy( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoH1Strategy();
	}

	public function getSeoTitle( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoTitle();
	}

	public function getSeoTitleStrategy( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoTitleStrategy();
	}

	public function getSeoDescription( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoDescription();
	}

	public function getSeoDescriptionStrategy( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getSeoDescriptionStrategy();
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

}