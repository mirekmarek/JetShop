<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\DataModel_Related_1toN;
use Jet\Form;
use Jet\Tr;

#[DataModel_Definition(
	name: 'parametrization_properties_shop_data',
	database_table_name: 'parametrization_properties_shop_data',
	id_controller_class: DataModel_IDController_Passive::class,
	parent_model_class: Parametrization_Property::class
)]
abstract class Core_Parametrization_Property_ShopData extends DataModel_Related_1toN implements Images_ShopDataInterface, CommonEntity_ShopDataInterface {

	use CommonEntity_ShopDataTrait;
	use Images_ShopDataTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
		form_field_type: false
	)]
	protected int $category_id = 0;

	protected Category|null $category = null;

	protected int|null $group_id = null;

	protected Parametrization_Group|null $group = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'parent.id',
		form_field_type: false
	)]
	protected int $property_id = 0;

	protected Parametrization_Property|null $property = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Name:',
		max_len: 255
	)]
	protected string $label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Description:',
		max_len: 65536
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type: false,
		max_len: 255
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_type: false,
		max_len: 255
	)]
	protected string $image_pictogram = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Description for YES:',
		max_len: 255
	)]
	protected string $bool_yes_description='';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'URL parameter:',
		max_len: 255
	)]
	protected string $url_param = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Units (mm, cm, ...):',
		max_len: 255
	)]
	protected string $units = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'SEO - H1:',
		max_len: 255
	)]
	protected string $seo_h1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: ' ',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [self::class,'getSeoStrategyOptions'],
		max_len: 50
	)]
	protected string $seo_h1_strategy = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'SEO - title:',
		max_len: 255
	)]
	protected string $seo_title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: ' ',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [self::class,'getSeoStrategyOptions'],
		max_len: 50
	)]
	protected string $seo_title_strategy = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'SEO - description:',
		max_len: 255
	)]
	protected string $seo_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: ' ',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [self::class,'getSeoStrategyOptions'],
		max_len: 50
	)]
	protected string $seo_description_strategy = '';

	protected static array $seo_strategy_options = array(
		Parametrization_Property::SEO_STRATEGY_ADD_AFTER  => 'Add before ',
		Parametrization_Property::SEO_STRATEGY_ADD_BEFORE => 'Add after ',
		Parametrization_Property::SEO_STRATEGY_DO_NOT_ADD => 'Do not add',
		Parametrization_Property::SEO_STRATEGY_REPLACE    => 'Replace',
	);

	public static function getSeoStrategyOptions() : array
	{
		return self::$seo_strategy_options;
	}

	public function setParents( Category $category, Parametrization_Group $group, Parametrization_Property $property ) : void
	{
		$this->category = $category;
		$this->category_id = $property->getCategoryId();

		$this->group = $group;
		$this->group_id = $group->getId();

		$this->property = $property;
		$this->property_id = $property->getId();
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

	public function setLabel( string $label ) : void
	{
		$this->label = $label;
	}

	public function getLabel() : string
	{
		return $this->label;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function getBoolYesDescription() : string
	{
		return $this->bool_yes_description;
	}

	public function setBoolYesDescription( string $bool_yes_description ) : void
	{
		$this->bool_yes_description = $bool_yes_description;
	}

	public function getUrlParam() : string
	{
		return $this->url_param;
	}

	public function setUrlParam( string $url_param ) : void
	{
		$this->url_param = $url_param;
	}

	public function getUnits() : string
	{
		return $this->units;
	}

	public function setUnits( string $units ) : void
	{
		$this->units = $units;
	}

	public function getSeoH1() : string
	{
		return $this->seo_h1;
	}

	public function setSeoH1( string $seo_h1 ) : void
	{
		$this->seo_h1 = $seo_h1;
	}

	public function getSeoH1Strategy() : string
	{
		return $this->seo_h1_strategy;
	}

	public function setSeoH1Strategy( string $seo_h1_strategy ) : void
	{
		$this->seo_h1_strategy = $seo_h1_strategy;
	}

	public function getSeoTitle() : string
	{
		return $this->seo_title;
	}

	public function setSeoTitle( string $seo_title ) : void
	{
		$this->seo_title = $seo_title;
	}

	public function getSeoTitleStrategy() : string
	{
		return $this->seo_title_strategy;
	}

	public function setSeoTitleStrategy( string $seo_title_strategy ) : void
	{
		$this->seo_title_strategy = $seo_title_strategy;
	}

	public function getSeoDescription() : string
	{
		return $this->seo_description;
	}

	public function setSeoDescription( string $seo_description ) : void
	{
		$this->seo_description = $seo_description;
	}

	public function getSeoDescriptionStrategy() : string
	{
		return $this->seo_description_strategy;
	}

	public function setSeoDescriptionStrategy( string $seo_description_strategy ) : void
	{
		$this->seo_description_strategy = $seo_description_strategy;
	}

	public function getImageEntity() : string
	{
		return 'category_pp';
	}

	public function getImageObjectId() : int|string
	{
		return $this->property_id;
	}

	public static function getImageClasses() : array
	{
		return [
			Category_ShopData::IMG_MAIN => Tr::_('Main image', [], Category::getManageModuleName() ),
			Category_ShopData::IMG_PICTOGRAM => Tr::_('Pictogram image', [], Category::getManageModuleName() ),
		];
	}

	public function setImageMain( string $image_main ) : void
	{
		$this->setImage( Category_ShopData::IMG_MAIN, $image_main);
	}

	public function getImageMain() : string
	{
		return $this->getImage( Category_ShopData::IMG_MAIN );
	}

	public function getImageMainUrl() : string
	{
		return $this->getImageUrl( Category_ShopData::IMG_MAIN );
	}

	public function getImageMainThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Category_ShopData::IMG_MAIN, $max_w, $max_h );
	}

	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->setImage( Category_ShopData::IMG_PICTOGRAM, $image_pictogram );
	}

	public function getImagePictogram() : string
	{
		return $this->getImage( Category_ShopData::IMG_PICTOGRAM );
	}

	public function getImagePictogramUrl() : string
	{
		return $this->getImageUrl( Category_ShopData::IMG_PICTOGRAM );
	}

	public function getImagePictogramThumbnailUrl( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl( Category_ShopData::IMG_PICTOGRAM, $max_w, $max_h );
	}

	public function getPossibleToEditImages() : bool
	{
		if($this->property->isInherited()) {
			return false;
		}

		if($this->category->getEditForm()->getIsReadonly()) {
			return false;
		}

		return true;
	}

}