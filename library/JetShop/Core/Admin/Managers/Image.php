<?php
namespace JetShop;

use JetApplication\EShop;
use JetApplication\EShopEntity_HasImageGallery_Interface;
use JetApplication\ImageGallery_Image;

interface Core_Admin_Managers_Image
{
	public function setEshopSyncMode( bool $eshop_sync_mode ): void;
	
	public function getEshopSyncMode(): bool;
	
	public function resetDefinedImages() : void;
	
	public function defineImage(
		string $entity,
		string|int $object_id,
		?string $image_class='',
		?string $image_title='',
		?callable $image_property_getter=null,
		?callable $image_property_setter=null,
		?EShop $eshop=null
	);
	
	public function uploadImage(
		string $tmp_file_path,
		string $file_name,
		string $entity,
		string|int $object_id,
		string $image_class,
		?EShop $eshop = null
	) : void;
	
	public function getEditable(): bool;
	
	public function setEditable( bool $editable ): void;
	
	public function handleSelectImageWidgets() : bool;
	
	public function renderMain() : string;
	
	public function renderImageWidgets( ?EShop $eshop=null ) : string;
	
	public function renderStandardManagement() : string;
	
	public function commonImageManager( string $entity, int $entity_id ) : string;

	
	public function handleImageGalleryManagement( EShopEntity_HasImageGallery_Interface $item ) : void;
	
	public function uploadImageGallery( EShopEntity_HasImageGallery_Interface $item, array $images ) : void;
	
	public function renderImageGalleryManagement() : string;
	
	public function getImageGalleryImageURL( ImageGallery_Image $image ): string;
	
	public function getImageGalleryImageThumbnailUrl( ImageGallery_Image $image, int $max_w, int $max_h ): string;
	
}