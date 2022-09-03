<?php
namespace JetShop;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Tr;

#[DataModel_Definition(
	name: 'property_shop_data',
	database_table_name: 'properties_shop_data',
	parent_model_class: Property::class
)]
abstract class Core_Property_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface {

	use Images_ShopDataTrait;

	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_id: true,
		related_to: 'main.id',
	)]
	protected int $property_id = 0;

	protected Property|null $property = null;

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
	


	public function setPropertyId( int $property_id ) : void
	{
		$this->property_id = $property_id;
	}

	public function getPropertyId() : int
	{
		return $this->property_id;
	}

	public function getProperty() : Property
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
	

	public function getImageEntity() : string
	{
		return 'property';
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

}