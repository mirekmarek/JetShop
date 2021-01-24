<?php
namespace JetShop;

use Jet\Form;

interface Core_Images_ShopDataInterface {

	public function getImageEntity() : string;

	public function getImageObjectId() : int;

	public static function getImageClasses() : array;

	public function getPossibleToEditImages() : bool;

	public function getImage( string $image_class ) : string;

	public function setImage( string $image_class, string $path ) : void;

	public function getImageUrl( string $image_class ) : string;

	public function getImageThumbnailUrl( string $image_class, int $max_w, int $max_h ) : string;

	public function getImageUploadForm( string $image_class ) : Form|null;

	public function catchImageUploadForm( string $image_class ) : bool;

	public function getImageDeleteForm( string $image_class ) : Form|null;

	public function catchImageDeleteForm( string $image_class ) : bool;

	public static function renderImageWidget_container_start() : string;

	public static function renderImageWidget_container_end() : string;

	public function renderImageWidget( string $image_class, string $title ) : string;

	public function renderImageWidget_Image( string $image_class ) : string;

	public function catchImageWidget(
		string $image_class,
		callable $onUpload,
		callable $onDelete
	) : void;

}