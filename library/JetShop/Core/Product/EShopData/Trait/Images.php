<?php
namespace JetShop;

use JetApplication\EShopEntity_HasImageGallery_Trait;
use JetApplication\ImageGallery_Image;

trait Core_Product_EShopData_Trait_Images
{
	use EShopEntity_HasImageGallery_Trait;
	
	/**
	 * @return ImageGallery_Image[]
	 */
	public function getImages(): array
	{
		if( $this->images === null ) {
			$this->images = ImageGallery_Image::getImages( $this );
			
			if(
				!$this->images &&
				$this->isVariant()
			) {
				$this->images = ImageGallery_Image::getImages( $this->getVariantMasterProduct() );
			}
		}
		
		return $this->images;
	}
}