<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithIDAndShopData_ShopData;
use JetApplication\PropertyGroup;

#[DataModel_Definition(
	name: 'property_group_shop_data',
	database_table_name: 'property_groups_shop_data',
	parent_model_class: PropertyGroup::class
)]
abstract class Core_PropertyGroup_ShopData extends Entity_WithIDAndShopData_ShopData
{

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