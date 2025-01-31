<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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