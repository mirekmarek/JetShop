<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\IO_File;
use JetApplication\EShopEntity_Basic;
use JetApplication\Files;
use JetApplication\Product;
use JetApplication\Product_File;

#[DataModel_Definition(
	name: 'product_files',
	database_table_name: 'product_files',
)]
abstract class Core_Product_File extends EShopEntity_Basic
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
	
	public function getURL() : string
	{
		return Files::Manager()->getFileURL( Product::getEntityType(), $this->product_id, $this->file );
	}
	
	
	public function getPath() : string
	{
		return Files::Manager()->getFilePath( Product::getEntityType(), $this->product_id, $this->file );
	}
	
	public function getSize() : int
	{
		return IO_File::getSize( $this->getPath() );
	}
	
	public static function upload( Product $product, int $kind_of_file_id, string $file_name, string $srouce_file_path ) : static
	{
		$file = Files::Manager()->uploadFile(
			$product::getEntityType(),
			$product->getId(),
			$file_name,
			$srouce_file_path
		);
		
		$new_file = new Product_File();
		$new_file->setProductId( $product->getId() );
		$new_file->setKindOfFileId( $kind_of_file_id );
		$new_file->setFile( $file );

		return $new_file;
		
	}
}
