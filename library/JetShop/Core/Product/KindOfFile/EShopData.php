<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\EShopEntity_HasImages_Interface;
use JetApplication\EShopEntity_HasImages_Trait;
use JetApplication\EShopEntity_Definition;
use JetApplication\EShopEntity_WithEShopData_EShopData;
use JetApplication\Product;

#[DataModel_Definition(
	name: 'products_kind_of_file_eshop_data',
	database_table_name: 'products_kind_of_file_eshop_data',
	parent_model_class: Product::class
)]
abstract class Core_Product_KindOfFile_EShopData extends EShopEntity_WithEShopData_EShopData implements EShopEntity_HasImages_Interface
{
	use EShopEntity_HasImages_Trait;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
	)]
	#[Form_Definition(
		type: Form_Field::TYPE_INPUT,
		label: 'Name:'
	)]
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
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
	#[EShopEntity_Definition(
		is_description: true
	)]
	protected string $description = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL
	)]
	protected bool $show_on_product_detail = true;
	
	
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
	
	public function getShowOnProductDetail(): bool
	{
		return $this->show_on_product_detail;
	}
	
	public function setShowOnProductDetail( bool $show_on_product_detail ): void
	{
		$this->show_on_product_detail = $show_on_product_detail;
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