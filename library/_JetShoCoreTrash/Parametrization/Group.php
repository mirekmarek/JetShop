<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Definition;

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
	protected int $priority = 0;

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
		label: 'Allow to compare products',
	)]
	protected bool $allow_compare = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Group_ShopData::class
	)]
	#[Form_Definition(is_sub_forms: true)]
	protected array $shop_data = [];

	protected bool $is_inherited = false;

	protected bool $is_first = false;

	protected bool $is_last = false;

	protected Form|null $_add_form = null;
	
	protected Form|null $_edit_form = null;


	public function __construct() {
		parent::__construct();

		$this->afterLoad();
	}


	public function afterLoad() : void
	{
		Parametrization_Group_ShopData::checkShopData( $this, $this->shop_data );
	}

	public function getArrayKeyValue() : string
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
		return Category::get($this->category_id);
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


	public function getShopData( ?Shops_Shop $shop=null ) : Parametrization_Group_ShopData
	{
		return $this->shop_data[$shop ? $shop->getKey() : Shops::getCurrent()->getKey()];
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
			$form = $this->createForm('property_group_add_form');

			$this->_add_form = $form;
		}

		return $this->_add_form;
	}

	public function catchAddForm() : bool
	{
		return $this->getAddForm()->catch();
	}

	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$form = $this->createForm('property_group_edit_form_'.$this->getId());

			if($this->isInherited()) {
				$form->setIsReadonly();
			}

			$this->_edit_form = $form;
		}

		return $this->_edit_form;
	}

	public function catchEditForm() : bool
	{
		return $this->getEditForm()->catch();
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

	public function getImageMainThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImageMainThumbnailUrl($max_w, $max_h);
	}

	public function getImagePictogram( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImagePictogram();
	}

	public function getImagePictogramUrl( ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImagePictogramUrl();
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, ?Shops_Shop $shop=null  ) : string
	{
		return $this->getShopData($shop)->getImagePictogramThumbnailUrl($max_w, $max_h);
	}
}