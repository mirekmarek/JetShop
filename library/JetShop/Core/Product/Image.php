<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_Basic;

#[DataModel_Definition(
	name: 'product_images',
	database_table_name: 'product_images',
)]
abstract class Core_Product_Image extends Entity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected string $product_id = '';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $image_index = 0;
	
	/**
	 * @var string
	 */
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255
	)]
	protected string $image_file = '';
	
	protected string $key = '';
	
	
	/**
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getImages( int $product_id ) : array
	{
		$_images = static::fetch(
			where_per_model: ['product_images'=>[
				'product_id' => $product_id
			]],
			order_by: ['image_index']
		);
		
		$images = [];
		
		foreach($_images as $img) {
			$images[] = $img;
		}
		
		return $images;
	}
	
	public function setProductId( int $id ) : void
	{
		$this->product_id = $id;
	}
	
	public function getProductId() : string
	{
		return $this->product_id;
	}
	
	public function setImageIndex( int $value ) : void
	{
		$this->image_index = $value;
	}
	
	public function getImageIndex() : int
	{
		return $this->image_index;
	}
	
	public function setImageFile( string $value ) : void
	{
		$this->image_file = $value;
	}
	
	public function getImageFile() : string
	{
		return $this->image_file;
	}
	
	public function __toString() : string
	{
		return $this->image_file;
	}
	
	public function afterUpdate(): void
	{
	}
	
	public function afterDelete(): void
	{
	}
	
	public function afterAdd(): void
	{
	}
	
}
