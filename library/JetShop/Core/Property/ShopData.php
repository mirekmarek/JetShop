<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithIDAndShopData_ShopData;
use JetApplication\Property;

#[DataModel_Definition(
	name: 'property_shop_data',
	database_table_name: 'properties_shop_data',
	parent_model_class: Property::class
)]
abstract class Core_Property_ShopData extends Entity_WithIDAndShopData_ShopData {


	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	protected string $label = '';

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
		max_len: 255
	)]
	protected string $image_main = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_pictogram = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Description for YES:',
	)]
	protected string $bool_yes_description='';

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
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Units (mm, cm, ...):',
	)]
	protected string $units = '';
	

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
	
	public function getImageMain(): string
	{
		return $this->image_main;
	}
	
	public function setImageMain( string $image_main ): void
	{
		$this->image_main = $image_main;
	}
	
	public function getImagePictogram(): string
	{
		return $this->image_pictogram;
	}
	
	public function setImagePictogram( string $image_pictogram ): void
	{
		$this->image_pictogram = $image_pictogram;
	}
	
	
	
}