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
use JetApplication\PropertyGroup;

#[DataModel_Definition(
	name: 'property_group_eshop_data',
	database_table_name: 'property_groups_eshop_data',
	parent_model_class: PropertyGroup::class
)]
abstract class Core_PropertyGroup_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:',
	)]
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
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