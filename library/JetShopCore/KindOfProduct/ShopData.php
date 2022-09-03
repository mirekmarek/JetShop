<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_IDController_Passive;
use Jet\Form_Definition;
use Jet\Form_Field;
use Jet\Tr;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product_shop_data',
	database_table_name: 'kind_of_product_shop_data',
	parent_model_class: KindOfProduct::class,
	id_controller_class: DataModel_IDController_Passive::class
)]
abstract class Core_KindOfProduct_ShopData extends CommonEntity_ShopData implements Images_ShopDataInterface
{
	
	use Images_ShopDataTrait;
	
	/**
	 * @var int
	 */
	#[DataModel_Definition(
		related_to: 'main.id',
		is_id: true,
		is_key: true
	)]
	protected int $kind_of_product_id = 0;
	
	/**
	 * @return int
	 */
	public function getKindOfProductId() : int
	{
		return $this->kind_of_product_id;
	}
	
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
	
	
	public function getImageEntity() : string
	{
		return 'property';
	}
	
	public function getImageObjectId() : int|string
	{
		return $this->kind_of_product_id;
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
