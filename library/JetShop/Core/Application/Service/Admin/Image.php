<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_Admin;
use JetApplication\EShop;
use JetApplication\ImageGallery;
use JetApplication\ImageGallery_Image;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Images',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_Image extends Application_Module
{
	abstract public function setEshopSyncMode( bool $eshop_sync_mode ): void;
	
	abstract public function getEshopSyncMode(): bool;
	
	abstract public function resetDefinedImages() : void;
	
	abstract public function defineImage(
		string $entity,
		string|int $object_id,
		?string $image_class='',
		?string $image_title='',
		?callable $image_property_getter=null,
		?callable $image_property_setter=null,
		?EShop $eshop=null
	);
	
	abstract public function uploadImage(
		string $tmp_file_path,
		string $file_name,
		string $entity,
		string|int $object_id,
		string $image_class,
		?EShop $eshop = null
	) : void;
	
	abstract public function getEditable(): bool;
	
	abstract public function setEditable( bool $editable ): void;
	
	abstract public function handleSelectImageWidgets() : bool;
	
	abstract public function renderMain() : string;
	
	abstract public function renderImageWidgets( ?EShop $eshop=null ) : string;
	
	abstract public function renderStandardManagement() : string;
	
	abstract public function commonImageManager( string $entity, int $entity_id ) : string;
	
	
	abstract public function handleImageGalleryManagement( ImageGallery $gallery ) : void;
	
	abstract public function uploadImageGallery( ImageGallery $gallery, array $images ) : void;
	
	abstract public function renderImageGalleryManagement() : string;
	
	abstract public function getImageGalleryImageURL( ImageGallery_Image $image ): string;
	
	abstract public function getImageGalleryImageThumbnailUrl( ImageGallery_Image $image, int $max_w, int $max_h ): string;
	
}