<?php
namespace JetShop;

use JetApplication\EShop;
use JetApplication\Product;
use JetApplication\Product_Image;

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

	
	
	
	public function handleProductImageManagement( Product $product ) : void;
	
	public function uploadProductImages( Product $product, array $images ) : void;
	
	public function renderProductImageManagement() : string;
	
	public function getProductImageURL( Product_Image $image ): string;
	
	public function getProductImageThumbnailUrl( Product_Image $image, int $max_w, int $max_h ): string;
	
}