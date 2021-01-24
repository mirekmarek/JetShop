<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;
use Jet\Form;

/**
 *
 *
 */
#[DataModel_Definition(
	name: 'parametrization_groups',
	database_table_name: 'parametrization_groups',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	parent_model_class: Category::class,
	default_order_by: ['priority']
)]
abstract class Core_Parametrization_Group extends DataModel_Related_1toN {

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
	protected int $priority = 0;

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

	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Group_ShopData::class
	)]
	protected $shop_data;

	protected bool $is_inherited = false;

	protected bool $is_first = false;

	protected bool $is_last = false;

	protected Category|null $category = null;

	protected Form|null $_add_form = null;
	
	protected Form|null $_edit_form = null;


	public function __construct() {
		parent::__construct();

		$this->afterLoad();
	}


	public function afterLoad() : void
	{
		foreach( Shops::getList() as $shop ) {
			$shop_id = $shop->getId();

			if(!isset($this->shop_data[$shop_id])) {

				$sh = new Parametrization_Group_ShopData();
				$sh->setShopId($shop_id);

				$this->shop_data[$shop_id] = $sh;
			}
		}
	}

	public function getArrayKeyValue() : int 
	{
		return $this->id;
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function setParents( Category $category ) : void
	{
		$this->category = $category;
		$this->category_id = $category->getId();

		foreach($this->shop_data as $shop_data) {
			$shop_data->setParents( $category, $this );
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

	public function getCategory() : Category
	{
		return $this->category;
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

	public function setIsInherited( bool $is_inherited ) : void
	{
		$this->is_inherited = $is_inherited;
	}

	public function setIsFirst( bool $is_first ) : void
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

	public function isInherited() : bool
	{
		return $this->is_inherited;
	}
	
	public function setAllowDisplay( bool $display_it ) : void
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
		if(!$this->getIsActive() || !$this->getAllowFilter()) {
			return false;
		}

		foreach( $this->getProperties() as $property ) {
			if($property->getIsFilterable()) {
				return true;
			}
		}

		return false;
	}

	public function setAllowCompare( bool $compare ) : void
	{
		$this->allow_compare = $compare;
	}

	public function getAllowCompare() : bool
	{
		return $this->allow_compare;
	}

	public function getShopData( string|null $shop_id=null ) : Parametrization_Group_ShopData
	{
		if(!$shop_id) {
			$shop_id = Shops::getCurrentId();
		}

		return $this->shop_data[$shop_id];
	}


	/**
	 * @return Parametrization_Property[]
	 */
	public function getProperties() : array 
	{
		return $this->getCategory()->getParametrizationProperties( $this->getId() );
	}

	public function getProperty( int $id ) : Parametrization_Property|null
	{
		$properties = $this->getCategory()->getParametrizationProperties( $this->getId() );
		if(!isset($properties[$id])) {
			return null;
		}

		return $properties[$id];
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$form = $this->getCommonForm('property_group_add_form');

			$this->_add_form = $form;
		}

		return $this->_add_form;
	}

	public function catchAddForm() : bool
	{
		$form = $this->getAddForm();

		if( !$form->catchInput() || !$form->validate() ) {
			return false;
		}

		$form->catchData();

		return true;
	}

	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$form = $this->getCommonForm('property_group_edit_form_'.$this->getId());

			if($this->isInherited()) {
				$form->setIsReadonly();
			}

			$this->_edit_form = $form;
		}

		return $this->_edit_form;
	}

	public function catchEditForm() : bool
	{
		$form = $this->getEditForm();

		if( !$form->catchInput() || !$form->validate() ) {
			return false;
		}

		$form->catchData();

		return true;
	}

	public function getLabel( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getLabel();
	}

	public function getDescription( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getDescription();
	}

	public function getImageMain( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImageMain();
	}

	public function getImageMainUrl( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImageMainUrl();
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h, string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImageMainThumbnailUrl($max_w, $max_h);
	}

	public function getImagePictogram( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImagePictogram();
	}

	public function getImagePictogramUrl( string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImagePictogramUrl();
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, string|null $shop_id=null ) : string
	{
		return $this->getShopData($shop_id)->getImagePictogramThumbnailUrl($max_w, $max_h);
	}
}