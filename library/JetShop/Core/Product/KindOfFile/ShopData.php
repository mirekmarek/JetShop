<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\Product;

#[DataModel_Definition(
	name: 'products_kind_of_file_shop_data',
	database_table_name: 'products_kind_of_file_shop_data',
	parent_model_class: Product::class
)]
abstract class Core_Product_KindOfFile_ShopData extends Entity_WithShopData_ShopData {
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	protected string $name = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Short description:'
	)]
	protected string $short_description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 65536,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_WYSIWYG,
		label: 'Description:'
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
	
	
	public function getName() : string
	{
		return $this->name;
	}
	
	public function setName( string $name ) : void
	{
		$this->name = $name;
	}
	
	public function getDescription() : string
	{
		return $this->description;
	}
	
	public function setDescription( string $description ) : void
	{
		$this->description = $description;
	}
	
	
	public function getShortDescription() : string
	{
		return $this->short_description;
	}
	
	public function setShortDescription( string $short_description ) : void
	{
		$this->short_description = $short_description;
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
	
	public function getImagePictogramURL() : string
	{
		return $this->getImageUrl('pictogram');
	}
	
	public function getImagePictogramThumbnailURL( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl('pictogram', $max_w, $max_h);
	}
	
	
	public function getImageMainURL() : string
	{
		return $this->getImageUrl('main');
	}
	
	public function getImageMainThumbnailURL( int $max_w, int $max_h ) : string
	{
		return $this->getImageThumbnailUrl('main', $max_w, $max_h);
	}
	
}