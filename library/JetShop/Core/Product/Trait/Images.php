<?php
namespace JetShop;

use JetApplication\Product_Image;

trait Core_Product_Trait_Images
{
	/**
	 * @var Product_Image[]
	 */
	protected ?array $images = null;
	
	/**
	 * @return Product_Image[]
	 */
	public function getImages() : array
	{
		if( $this->images===null ) {
			$this->images = Product_Image::getImages( $this->id );
		}
		
		return $this->images;
	}
	
	public function getImage( int $index ) : ?Product_Image
	{
		$this->getImages();
		
		return $this->images[$index]??null;
	}
	
	public function getImageByKey( string $key ) : ?Product_Image
	{
		$this->getImages();
		
		foreach($this->images as $image) {
			if($image->getKey()==$key) {
				return $image;
			}
		}
		
		return null;
	}
	
	public function deleteImage( string $key ) : ?Product_Image
	{
		$this->getImages();
		
		$deleted_image = $this->getImageByKey( $key );
		if(!$deleted_image) {
			return null;
		}
		
		$index = $deleted_image->getImageIndex();
		
		$deleted_image->delete();
		
		unset( $this->images[$index] );
		
		$i = 0;
		foreach($this->images as $img) {
			$img->setImageIndex( $i );
			$img->save();
			
			$i++;
		}
		
		return $deleted_image;
	}
	
	public function addImage( string $file ) : void
	{
		$this->getImages();
		
		$new_image = new Product_Image();
		$new_image->setProductId( $this->getId() );
		$new_image->setImageFile( $file );
		$new_image->setImageIndex( count($this->images) );
		$new_image->save();
		
		$this->images[] = $new_image;
	}
	
	public function sortImages( array $image_keys ) : void
	{
		$image_keys = array_unique($image_keys);
		
		$this->getImages();
		
		$sorted_images = [];
		
		foreach($image_keys as $image_key) {
			$image = $this->getImageByKey( $image_key );
			if(!$image) {
				return;
			}
			$sorted_images [] = $image;
		}
		
		if(count($sorted_images)!=count($this->images)) {
			return;
		}
		
		
		/**
		 * @var Product_Image[] $sorted_images
		 */
		foreach($sorted_images as $i=>$image) {
			$image->setImageIndex( $i );
			$image->save();
		}
		
		$this->images = $sorted_images;
	}
}