<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;



interface Core_EShopEntity_HasImages_Interface {
	
	public function getImage( string $image_class ) : string;
	
	public function setImage( string $image_class, $image ) : void;
	
	public function getImageThumbnailUrl( string $image_class, int $max_w, int $max_h ): string;
	
	public function getImageUrl( string $image_class ): string;
	
	public function defineImages() : void;
	
	public function handleImages() : void;
	
	public function defineImage( string $image_class, string $image_title ) : void;
	
	public function uploadImage( string $image_class, string $tmp_file_path, string $file_name ) : void;
	
}