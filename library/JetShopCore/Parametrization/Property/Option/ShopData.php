<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\DataModel;
use Jet\Tr;

#[DataModel_Definition(
	name: 'parametrization_properties_options_shop_data',
	database_table_name: 'parametrization_properties_options_shop_data',
	parent_model_class: Parametrization_Property_Option::class
)]
abstract class Core_Parametrization_Property_Option_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface {

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

	protected int|null $property_id = null;

	protected Parametrization_Property|null $property = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'parent.id',
		form_field_type: false
	)]
	protected int $option_id = 0;

	protected Parametrization_Property_Option|null $option = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Filter label:',
		max_len: 255
	)]
	protected string $filter_label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Product detail label:',
		max_len: 255
	)]
	protected string $product_detail_label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'URL parameter:',
		max_len: 255
	)]
	protected string $url_param = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Description:',
		max_len: 65536
	)]
	protected string $description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'SEO - H1:',
		max_len: 255
	)]
	protected string $seo_h1 = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'SEO - title:',
		max_len: 255
	)]
	protected string $seo_title = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'SEO - description:',
		max_len: 255
	)]
	protected string $seo_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: 'Alternative category description:',
		max_len: 65536
	)]
	protected string $alternative_category_description = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		form_field_label: ' ',
		form_field_type: Form::TYPE_SELECT,
		form_field_get_select_options_callback: [ Parametrization_Property_ShopData::class,'getSeoStrategyOptions'],
		max_len: 50
	)]
	protected string $alternative_category_description_strategy = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
		form_field_label: 'Keywords words for internal fulltext:'
	)]
	protected string $internal_fulltext_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		form_field_type: false
	)]
	protected string $image_pictogram = '';

	public function getCategoryId() : int
	{
		return $this->category_id;
	}

	public function setCategoryId( int $category_id ) : void
	{
		$this->category_id = $category_id;
	}

	public function getGroupId() : int
	{
		return $this->group_id;
	}

	public function setGroupId( int $group_id ) : void
	{
		$this->group_id = $group_id;
	}

	public function getPropertyId() : int
	{
		return $this->property_id;
	}

	public function setPropertyId( int $property_id ) : void
	{
		$this->property_id = $property_id;
	}

	public function getOptionId() : int
	{
		return $this->option_id;
	}

	public function setOptionId( int $option_id ) : void
	{
		$this->option_id = $option_id;
	}

	public function getOption() : Parametrization_Property_Option
	{
		return $this->option;
	}

	public function setOption( Parametrization_Property_Option $option ) : void
	{
		$this->option = $option;
		$this->option_id = $option->getId();
	}

	public function getFilterLabel() : string
	{
		return $this->filter_label;
	}

	public function setFilterLabel( string $filter_label ) : void
	{
		$this->filter_label = $filter_label;
	}

	public function getProductDetailLabel() : string
	{
		return $this->product_detail_label;
	}

	public function setProductDetailLabel( string $product_detail_label ) : void
	{
		$this->product_detail_label = $product_detail_label;
	}

	public function getUrlParam() : string
	{
		return $this->url_param;
	}

	public function setUrlParam( string $url_param ) : void
	{
		$this->url_param = $url_param;
	}

	public function getDescription() : string
	{
		return $this->description;
	}

	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}

	public function getSeoH1() : string
	{
		return $this->seo_h1;
	}

	public function setSeoH1( string $seo_h1 ) : void
	{
		$this->seo_h1 = $seo_h1;
	}

	public function getSeoTitle() : string
	{
		return $this->seo_title;
	}

	public function setSeoTitle( string $seo_title ) : void
	{
		$this->seo_title = $seo_title;
	}

	public function getSeoDescription() : string
	{
		return $this->seo_description;
	}

	public function setSeoDescription( string $seo_description ) : void
	{
		$this->seo_description = $seo_description;
	}

	public function getAlternativeCategoryDescription() : string
	{
		return $this->alternative_category_description;
	}

	public function setAlternativeCategoryDescription( string $alternative_category_description ) : void
	{
		$this->alternative_category_description = $alternative_category_description;
	}

	public function getAlternativeCategoryDescriptionStrategy() : string
	{
		return $this->alternative_category_description_strategy;
	}

	public function setAlternativeCategoryDescriptionStrategy( string $alternative_category_description_strategy ) : void
	{
		$this->alternative_category_description_strategy = $alternative_category_description_strategy;
	}

	public function getInternalFulltextKeywords() : string
	{
		return $this->internal_fulltext_keywords;
	}

	public function setInternalFulltextKeywords( string $internal_fulltext_keywords ) : void
	{
		$this->internal_fulltext_keywords = $internal_fulltext_keywords;
	}

	public function getImageEntity() : string
	{
		return 'category_ppo';
	}

	public function getImageObjectId() : int|string
	{
		return $this->option_id;
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

}