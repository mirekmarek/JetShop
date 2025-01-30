<?php
namespace JetShop;

use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShops;
use JetApplication\EShopEntity_Definition;

trait Core_EShopEntity_WithEShopData_HasImages_Trait {
	
	public function defineImages(): void
	{
		$images = EShopEntity_Definition::get( $this )?->getImages();
		if(!$images) {
			return;
		}
		
		foreach($images as $image_class=>$title) {
			$this->defineImage( $image_class, $title );
		}
		
	}
	
	public function getImage( string $image_class ) : string
	{
		return '';
	}
	
	public function getImageThumbnailUrl( string $image_class, int $max_w, int $max_h ): string
	{
		return '';
	}
	
	public function getImageUrl( string $image_class ): string
	{
		return '';
	}
	
	public function handleImages() : void
	{
		/**
		 * @var EShopEntity_Admin_Interface $this
		 */
		Application_Admin::handleUploadTooLarge();
		
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $this->isEditable() );
		$manager->handleSelectImageWidgets();
	}
	
	public function defineImage( string $image_class, string $image_title ) : void
	{
		foreach(EShops::getListSorted() as $eshop) {
			$sd = $this->getEshopData( $eshop );
			$sd->defineImage( $image_class, $image_title );
		}
		
	}
	
	public function setImage( string $image_class, $image ) : void
	{
		foreach(EShops::getListSorted() as $eshop) {
			$sd = $this->getEshopData( $eshop );
			$sd->setImage( $image_class, $image );
		}
	}
	
	
	public function uploadImage( string $image_class, string $tmp_file_path, string $file_name ) : void
	{
		foreach(EShops::getListSorted() as $eshop) {
			$sd = $this->getEshopData( $eshop );
			$sd->uploadImage( $image_class, $tmp_file_path, $file_name );
		}
	}
	
}