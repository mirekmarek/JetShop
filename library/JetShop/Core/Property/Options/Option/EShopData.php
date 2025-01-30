<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Property_Options_Option;

#[DataModel_Definition(
	name: 'properties_options_eshop_data',
	database_table_name: 'properties_options_eshop_data',
	parent_model_class: Property_Options_Option::class
)]
abstract class Core_Property_Options_Option_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $option_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $priority = 0;
	

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Filter label:',
	)]
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';
	
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
	
	public function getPriority(): int
	{
		return $this->priority;
	}
	
	public function setPriority( int $priority ): void
	{
		$this->priority = $priority;
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
		if($this->filter_label==$filter_label) {
			return;
		}
		$this->filter_label = $filter_label;
		//TODO: $this->url_param = $this->_generateURLParam( $this->filter_label );
		
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