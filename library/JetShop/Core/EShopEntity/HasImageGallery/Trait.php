<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use JetApplication\ImageGallery;
use JetApplication\ImageGallery_Image;

trait Core_EShopEntity_HasImageGallery_Trait {
	
	protected ?ImageGallery $image_gallery = null;

	public function getImageGallery(): ImageGallery
	{
		if(!$this->image_gallery) {
			$this->image_gallery = new ImageGallery( $this->getEntityTypeForImageGallery(), $this->getEntityIdForImageGallery() );
		}
		
		return $this->image_gallery;
	}
	
	/**
	 * @return ImageGallery_Image[]
	 */
	public function getImages() : array
	{
		return $this->getImageGallery()->getImages();
	}
	
	public function getImage( int $image_index ) : ?ImageGallery_Image
	{
		return $this->getImageGallery()->getImage( $image_index );
	}
	
	public function getImageThumbnailUrl( int $image_index, int $max_w, int $max_h ): string
	{
		return $this->getImage( $image_index )?->getThumbnailUrl( $max_w, $max_h )??'';
	}
	
	public function getImageURL( int $image_index ): string
	{
		return $this->getImage( $image_index )?->getURL()??'';
	}
	
}