<?php
namespace JetShop;


use JetApplication\Product_Image;
use JetApplication\EShop_Managers;

trait Core_Product_EShopData_Trait_Images
{
	/**
	 * @var Product_Image[]
	 */
	protected ?array $images = null;
	
	
	/**
	 * @return Product_Image[]
	 */
	public function getAllImages(): array
	{
		if( $this->images === null ) {
			$this->images = Product_Image::getImages( $this->entity_id );
			
			if(
				!$this->images &&
				$this->isVariant()
			) {
				$this->images = Product_Image::getImages( $this->getVariantMasterProductId() );
			}
		}
		
		return $this->images;
	}
	
	public function getImg( int $image_index ): string
	{
		$this->getAllImages();
		
		$image = $this->images[$image_index] ?? null;
		
		return $image?->getImageFile() ? : '';
	}
	
	
	public function getImgThumbnailUrl( int $image_index, int $max_w, int $max_h ): string
	{
		return EShop_Managers::Image()->getThumbnailUrl(
			$this->getImg( $image_index ),
			$max_w,
			$max_h
		);
	}
	
	public function getImgUrl( int $image_index ): string
	{
		return EShop_Managers::Image()->getUrl(
			$this->getImg( $image_index )
		);
	}

	
}