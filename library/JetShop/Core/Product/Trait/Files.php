<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Files;
use JetApplication\Product;
use JetApplication\Product_File;

trait Core_Product_Trait_Files
{
	/**
	 * @var Product_File[]
	 */
	protected ?array $files = null;
	
	/**
	 * @return Product_File[]
	 */
	public function getFiles() : array
	{
		if( $this->files===null ) {
			if($this->getType()==Product::PRODUCT_TYPE_SET) {
				$this->files = [];
				foreach( $this->getSetItems() as $set_item ) {
					$set_item = Product::load( $set_item->getItemProductId() );
					foreach($set_item->getFiles() as $file) {
						$this->files[] = $file;
					}
				}
				
				return $this->files;
			}
			
			$id = $this->getId();
			
			if($this->getType()==Product::PRODUCT_TYPE_VARIANT) {
				$id = $this->getVariantMasterProductId();
			}
			
			$this->files = Product_File::getFiles( $id );
		}
		
		return $this->files;
	}
	
	
	public function getFile( int $id ) : ?Product_File
	{
		$this->getFiles();
		return $this->files[$id]??null;
	}
	
	public function deleteFile( int $id ) : ?Product_File
	{
		
		$deleted_file = $this->getFile( $id );
		if(!$deleted_file) {
			return null;
		}
		
		Files::Manager()->deleteFile( $this::getEntityType(), $this->getId(), $deleted_file->getFile() );
		
		$deleted_file->delete();
		
		
		$this->files = null;
		
		$this->getFiles();
		
		$i = 0;
		foreach( $this->files as $img) {
			$img->setFileIndex( $i );
			$img->save();
			
			$i++;
		}
		
		return $deleted_file;
	}
	
	public function addFile( int $kind_of_file_id, string $file_name, string $srouce_file_path ) : Product_File
	{
		/**
		 * @var Product $this
		 */
		$new_file = Product_File::upload( $this, $kind_of_file_id, $file_name, $srouce_file_path );
		
		foreach( $this->getFiles() as $e_file ) {
			if( $e_file->getFile()==$new_file->getFile() ) {
				$e_file->setKindOfFileId( $kind_of_file_id );
				$e_file->save();
				
				return $e_file;
			}
		}
		
		$new_file->setFileIndex( count($this->files) );
		$new_file->save();
		
		$this->files[$new_file->getId()] = $new_file;
		
		return $new_file;
	}
	
	public function sortFiles( array $file_ids ) : void
	{
		$file_ids = array_unique($file_ids);
		$this->getFiles();
		
		$i = 0;
		foreach( $file_ids as $file_id) {
			$file = $this->getFile( $file_id );
			if(!$file) {
				return;
			}
			$file->setFileIndex( $i );
			$file->save();
			
			$i++;
		}
		
		$this->files = null;
	}
	
	public function cloneFiles( Product $source_product ) : void
	{
		foreach($source_product->getFiles() as $file) {
			$path_i = pathinfo( $file->getFile() );
			$file_name = $path_i['filename'].'_c.'.$path_i['extension'];
			
			$this->addFile(
				$file->getKindOfFileId(),
				$file_name,
				$file->getPath()
			);
			
		}
	}
}