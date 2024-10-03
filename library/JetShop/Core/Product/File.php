<?php
/**
 *
 */

namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use JetApplication\Entity_Basic;

#[DataModel_Definition(
	name: 'product_files',
	database_table_name: 'product_files',
)]
abstract class Core_Product_File extends Entity_Basic
{
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $product_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $kind_of_file_id = 0;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 255,
		is_key: true
	)]
	protected string $file = '';
	
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true
	)]
	protected int $file_index = 0;
	
	
	/**
	 * @param int $product_id
	 * @return static[]
	 */
	public static function getFiles( int $product_id ) : array
	{
		$_images = static::fetch(
			where_per_model: ['product_files'=>[
				'product_id' => $product_id
			]],
			order_by: ['file_index']
		);
		
		$images = [];
		
		foreach($_images as $img) {
			$images[$img->getId()] = $img;
		}
		
		return $images;
	}
	
	public function setProductId( int $id ) : void
	{
		$this->product_id = $id;
	}
	
	public function getProductId() : int
	{
		return $this->product_id;
	}
	
	public function setKindOfFileId( int $value ) : void
	{
		$this->kind_of_file_id = $value;
	}
	
	public function getKindOfFileId() : int
	{
		return $this->kind_of_file_id;
	}
	
	public function setFile( string $value ) : void
	{
		$this->file = $value;
	}
	
	public function getFile() : string
	{
		return $this->file;
	}
	
	public function getFileIndex(): int
	{
		return $this->file_index;
	}
	
	public function setFileIndex( int $file_index ): void
	{
		$this->file_index = $file_index;
	}
	
	public function __toString() : string
	{
		return $this->file;
	}
}
