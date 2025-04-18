<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_Managers;
use JetApplication\EShop_Managers;
use JetApplication\EShopEntity_Basic;
use JetApplication\EShopEntity_HasImageGallery_Interface;
use JetApplication\Product;
use JetApplication\ImageGallery_Image;

trait Core_EShopEntity_HasImageGallery_Trait {
	
	/**
	 * @var ImageGallery_Image[]
	 */
	protected ?array $images = null;
	
	/**
	 * @return ImageGallery_Image[]
	 */
	public function getImages() : array
	{
		if( $this->images===null ) {
			$this->images = ImageGallery_Image::getImages( $this );
		}
		
		return $this->images;
	}
	
	public function getImage( int $index ) : ?ImageGallery_Image
	{
		$this->getImages();
		
		return $this->images[$index]??null;
	}
	
	public function getImageById( int $id ) : ?ImageGallery_Image
	{
		$this->getImages();
		
		foreach($this->images as $image) {
			if($image->getId()==$id) {
				return $image;
			}
		}
		
		return null;
	}
	
	public function deleteImage( int $id ) : ?ImageGallery_Image
	{
		$this->getImages();
		
		$deleted_image = $this->getImageById( $id );
		if(!$deleted_image) {
			return null;
		}
		
		$deleted_image->delete();
		
		$this->images = null;
		$this->getImages();
		
		$index = 0;
		foreach($this->images as $img) {
			$img->setImageIndex( $index );
			$img->save();
			$index++;
		}
		
		return $deleted_image;
	}
	
	public function addImage( string $file ) : void
	{
		$this->getImages();
		
		$new_image = new ImageGallery_Image();
		$new_image->setImageGalleryEntityType( $this->getEntityTypeForImageGallery() );
		$new_image->setImageGalleryEntityId( $this->getEntityIdForImageGallery() );
		$new_image->setImageFile( $file );
		$new_image->setImageIndex( count($this->images) );
		$new_image->save();
		
		$this->images[] = $new_image;
	}
	
	public function sortImages( array $image_ids ) : void
	{
		$image_ids = array_unique($image_ids);
		$this->getImages();
		
		$i = 0;
		foreach($image_ids as $id) {
			$image = $this->getImageById( $id );
			if(!$image) {
				continue;
			}
			
			$image->setImageIndex( $i );
			$image->save();
			
			$i++;
		}
		
		$this->images = null;
	}
	
	public function uploadImages( array $images ) : void
	{
		/**
		 * @var Product $this
		 */
		Admin_Managers::Image()->uploadImageGallery( $this, $images );
	}
	
	public function getImageThumbnailUrl( int $image_index, int $max_w, int $max_h ): string
	{
		$image = $this->getImage( $image_index );
		if(!$image) {
			return '';
		}
		
		return EShop_Managers::Image()->getThumbnailUrl(
			$image->getImageFile(),
			$max_w,
			$max_h
		);
	}
	
	public function getImageURL( int $image_index ): string
	{
		$image = $this->getImage( $image_index );
		if(!$image) {
			return '';
		}
		
		return EShop_Managers::Image()->getUrl(
			$image->getImageFile()
		);
	}
	
	public function cloneImages( EShopEntity_Basic|EShopEntity_HasImageGallery_Interface $source ) : void
	{
		$images = [];
		foreach($source->getImages() as $source_image) {
			
			$image_path = EShop_Managers::Image()->getPath(
				$source_image->getImageFile()
			);
			
			$path_i = pathinfo( $source_image->getImageFile() );
			$file_name = $path_i['filename'].'_c.'.$path_i['extension'];
			
			$images[$image_path] = $file_name;
		}
		
		$this->uploadImages( $images );
		
		

	}
	
}