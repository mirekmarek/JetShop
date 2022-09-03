<?php
namespace JetShop;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\DataModel;
use Jet\Tr;

#[DataModel_Definition(
	name: 'properties_options_shop_data',
	database_table_name: 'properties_options_shop_data',
	parent_model_class: Property_Options_Option::class
)]
abstract class Core_Property_Options_Option_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface {

	use Images_ShopDataTrait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
		related_to: 'main.id'
	)]
	protected int $property_id = 0;

	protected Property|null $property = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'parent.id',
	)]
	protected int $option_id = 0;

	protected Property_Options_Option|null $option = null;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Filter label:',
	)]
	protected string $filter_label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Product detail label:',
	)]
	protected string $product_detail_label = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'URL parameter:',
	)]
	protected string $url_param = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Description:',
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 999999,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_TEXTAREA,
		label: 'Keywords for internal fulltext:'
	)]
	protected string $internal_fulltext_keywords = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
	)]
	protected string $image_pictogram = '';


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

	public function getOption() : Property_Options_Option
	{
		return $this->option;
	}

	public function setOption( Property_Options_Option $option ) : void
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