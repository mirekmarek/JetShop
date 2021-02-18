<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Form_Field_Select;
use Jet\DataModel;
use Jet\DataModel_IDController_AutoIncrement;
use Jet\DataModel_Related_1toN;

#[DataModel_Definition(
	name: 'parametrization_properties_options',
	database_table_name: 'parametrization_properties_options',
	id_controller_class: DataModel_IDController_AutoIncrement::class,
	id_controller_options: ['id_property_name'=>'id'],
	default_order_by: ['priority'],
	parent_model_class: Core_Parametrization_Property::class
)]
abstract class Core_Parametrization_Property_Option extends DataModel_Related_1toN {

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

	protected Category|null $category = null;

	protected int|null $group_id = null;

	protected Parametrization_Group|null $group = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false,
		related_to: 'parent.id'		
	)]
	protected int $property_id = 0;

	protected Parametrization_Property|null $property = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		form_field_label: 'Is active'
	)]
	protected bool $is_active = true;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		form_field_type: false
	)]
	protected int $priority = 0;


	/**
	 * @var Parametrization_Property_Option_ShopData[]
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_DATA_MODEL,
		data_model_class: Parametrization_Property_Option_ShopData::class
	)]
	protected $shop_data;

	protected bool $is_first = false;

	protected bool $is_last = false;

	protected Form|null $_add_form = null;

	protected Form|null $_edit_form = null;

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

				$sh = new Parametrization_Property_Option_ShopData();
				$sh->setShopCode($shop_code);

				$this->shop_data[$shop_code] = $sh;
			}
		}
	}

	public function setParents( Category $category, Parametrization_Group $group, Parametrization_Property $property ) : void
	{
		$this->category = $category;
		$this->category_id = $property->getCategoryId();

		$this->group = $group;
		$this->group_id = $group->getId();

		$this->property = $property;
		$this->property_id = $property->getId();

		foreach($this->shop_data as $shop_data) {
			/** @noinspection PhpParamsInspection */
			$shop_data->setParents( $category, $group, $property, $this );
		}

	}

	public function isInherited() : bool
	{
		return $this->property->isInherited();
	}

	public function setCategoryId( int $category_id ) : void
	{
		$this->category_id = $category_id;
	}

	public function getCategoryId() : int
	{
		return $this->category_id;
	}

	public function getCategory() : Category
	{
		return $this->category;
	}

	public function setGroupId( int $group_id ) : void
	{
		$this->group_id = $group_id;
	}

	public function getGroupId() : int
	{
		return $this->group_id;
	}

	public function getGroup() : Parametrization_Group
	{
		return $this->group;
	}

	public function setPropertyId( int $property_id ) : void
	{
		$this->property_id = $property_id;
	}

	public function getPropertyId() : int
	{
		return $this->property_id;
	}

	public function getProperty() : Parametrization_Property
	{
		return $this->property;
	}

	public function getId() : int
	{
		return $this->id;
	}

	public function setId( int $id ) : void
	{
		$this->id = $id;
	}

	public function getArrayKeyValue() : int|string|null 
	{
		return $this->id;
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

	public function getShopData( string|null $shop_code=null ) : Parametrization_Property_Option_ShopData
	{
		if(!$shop_code) {
			$shop_code = Shops::getCurrentCode();
		}

		return $this->shop_data[$shop_code];
	}


	/**
	 * @param Parametrization_Property_Option_ShopData[] $shop_data
	 */
	public function setShopData( array $shop_data )
	{
		$this->shop_data = $shop_data;
	}

	public function isFirst() : bool
	{
		return $this->is_first;
	}

	public function setIsFirst( bool $is_first ) : void
	{
		$this->is_first = $is_first;
	}

	public function isLast() : bool
	{
		return $this->is_last;
	}

	public function setIsLast( bool $is_last ) : void
	{
		$this->is_last = $is_last;
	}

	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$form = $this->getCommonForm('option_add_form');

			foreach( Shops::getList() as $shop ) {
				$shop_code = $shop->getCode();

				$seo_description_strategy = $form->field('/shop_data/'.$shop_code.'/alternative_category_description_strategy');
				$seo_description_strategy->setErrorMessages([
					Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
				]);
			}

			$this->_add_form = $form;
		}

		return $this->_add_form;
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
			$form = $this->getCommonForm('option_edit_form_'.$this->id);

			foreach( Shops::getList() as $shop ) {
				$shop_code = $shop->getCode();

				$seo_description_strategy = $form->field('/shop_data/'.$shop_code.'/alternative_category_description_strategy');
				$seo_description_strategy->setErrorMessages([
					Form_Field_Select::ERROR_CODE_INVALID_VALUE => 'Please select value',
				]);
			}

			$this->_edit_form = $form;

			if($this->getProperty()->isInherited()) {
				$this->_edit_form->setIsReadonly();
			}

		}

		return $this->_edit_form;
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

	public function getFilterLabel( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getFilterLabel();
	}

	public function getProductDetailLabel( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getProductDetailLabel();
	}

	public function getUrlParam( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getUrlParam();
	}

	public function getDescription( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getDescription();
	}
	
	public function getSeoH1( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getSeoH1();
	}

	public function getSeoTitle( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getSeoTitle();
	}

	public function getSeoDescription( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getSeoDescription();
	}

	public function getImageMain( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getImageMain();
	}

	public function getImageMainUrl( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getImageMainUrl();
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h, string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getImageMainThumbnailUrl( $max_w, $max_h );
	}

	public function getImagePictogram( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getImagePictogram();
	}

	public function getImagePictogramUrl( string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getImagePictogramUrl();
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h, string|null $shop_code=null ) : string
	{
		return $this->getShopData( $shop_code )->getImagePictogramThumbnailUrl( $max_w, $max_h );
	}

}