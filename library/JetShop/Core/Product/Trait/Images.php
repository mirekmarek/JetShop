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
	
	public function getImageById( int $id ) : ?Product_Image
	{
		$this->getImages();
		
		foreach($this->images as $image) {
			if($image->getId()==$id) {
				return $image;
			}
		}
		
		return null;
	}
	
	public function deleteImage( int $id ) : ?Product_Image
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
		
		$new_image = new Product_Image();
		$new_image->setProductId( $this->getId() );
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
}