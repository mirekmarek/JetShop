<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\EShopEntity_HasImageGallery_Trait;
use JetApplication\ImageGallery;

trait Core_Product_EShopData_Trait_Images
{
	use EShopEntity_HasImageGallery_Trait;
	
	public function getImageGallery(): ImageGallery
	{
		if(!$this->image_gallery) {
			$this->image_gallery = new ImageGallery( $this->getEntityTypeForImageGallery(), $this->getEntityIdForImageGallery() );
			
			if(
				$this->isVariant() &&
				!$this->image_gallery->getImages()
			) {
				$this->image_gallery = new ImageGallery( $this->getEntityTypeForImageGallery(), $this->getVariantMasterProductId() );
			}
		}
		
		return $this->image_gallery;
	}
	
}