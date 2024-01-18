<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form_Definition;
use Jet\Form_Field;
use JetApplication\Entity_WithShopData_ShopData;
use JetApplication\KindOfProduct;
use JetApplication\KindOfProduct_Property;

/**
 *
 */
#[DataModel_Definition(
	name: 'kind_of_product_shop_data',
	database_table_name: 'kind_of_product_shop_data',
	parent_model_class: KindOfProduct::class,
)]
abstract class Core_KindOfProduct_ShopData extends Entity_WithShopData_ShopData
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
	
	
	public function getImageEntity() : string
	{
		return 'property';
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
	
	public static function getFilterablePropertyIds( array $kind_of_product_ids ) : array
	{
		$property_ids = KindOfProduct_Property::dataFetchCol(
			select: ['property_id'],
			where: [
				'kind_of_product_id' => $kind_of_product_ids,
				'AND',
				'can_be_filter' => true
			]
		);
		
		$property_ids = array_unique( $property_ids );
		
		return $property_ids;
	}
}
