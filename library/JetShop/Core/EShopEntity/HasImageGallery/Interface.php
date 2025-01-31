<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\ImageGallery_Image;

interface Core_EShopEntity_HasImageGallery_Interface {
	
	public function getEntityTypeForImageGallery() : string;
	public function getEntityIdForImageGallery() : string|int;
	
	/**
	 * @return ImageGallery_Image[]
	 */
	public function getImages() : array;
	
	public function getImage( int $index ) : ?ImageGallery_Image;
	public function getImageById( int $id ) : ?ImageGallery_Image;
	public function getImageThumbnailUrl( int $image_index, int $max_w, int $max_h ): string;
	public function getImageURL( int $image_index ): string;

	
	public function deleteImage( int $id ) : ?ImageGallery_Image;
	public function addImage( string $file ) : void;
	public function sortImages( array $image_ids ) : void;
	public function uploadImages( array $images ) : void;
	
}