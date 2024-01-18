<?php
namespace JetShop;


use JetApplication\Product_Image;
use JetApplication\Shop_Managers;
use Exception;

trait Core_Product_ShopData_Trait_Images
{
	/**
	 * @var Product_Image[]
	 */
	protected ?array $images = null;
	
	public function getImage( string $image_class ) : string
	{
		throw new Exception('Product entity has special image methods. Use: getAllImages, getImg, getImgThumbnailUrl, getImgUrl');
	}
	
	public function setImage( string $image_class, $image ) : void
	{
		throw new Exception('Product entity has special image methods. Use: getAllImages, getImg, getImgThumbnailUrl, getImgUrl');
	}
	
	public function getImageThumbnailUrl( string $image_class, int $max_w, int $max_h ): string
	{
		throw new Exception('Product entity has special image methods. Use: getAllImages, getImg, getImgThumbnailUrl, getImgUrl');
	}
	
	public function getImageUrl( string $image_class ): string
	{
		throw new Exception('Product entity has special image methods. Use: getAllImages, getImg, getImgThumbnailUrl, getImgUrl');
	}

	
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
		return Shop_Managers::Image()->getThumbnailUrl(
			$this->getImg( $image_index ),
			$max_w,
			$max_h
		);
	}
	
	public function getImgUrl( int $image_index ): string
	{
		return Shop_Managers::Image()->getUrl(
			$this->getImg( $image_index )
		);
	}

	
}