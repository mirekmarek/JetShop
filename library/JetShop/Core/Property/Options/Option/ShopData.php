<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithIDAndShopData_ShopData;
use JetApplication\Property_Options_Option;

#[DataModel_Definition(
	name: 'properties_options_shop_data',
	database_table_name: 'properties_options_shop_data',
	parent_model_class: Property_Options_Option::class
)]
abstract class Core_Property_Options_Option_ShopData extends Entity_WithIDAndShopData_ShopData
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $option_id = 0;

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



	public function getOptionId() : int
	{
		return $this->option_id;
	}

	public function setOptionId( int $option_id ) : void
	{
		$this->option_id = $option_id;
	}

	public function setOption( Property_Options_Option $option ) : void
	{
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
	
	public function setImageMain( string $image_main ) : void
	{
		$this->image_main = $image_main;
	}

	public function getImageMain() : string
	{
		return $this->image_main;
	}
	
	public function setImagePictogram( string $image_pictogram ) : void
	{
		$this->image_pictogram = $image_pictogram;
	}

	public function getImagePictogram() : string
	{
		return $this->image_pictogram;
	}
}