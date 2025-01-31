<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;
use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\EShopEntity_HasEShopRelation_Interface;
use JetApplication\EShop_Managers;
use JetApplication\EShopEntity_Definition;

trait Core_EShopEntity_HasImages_Trait {
	
	public function getImage( string $image_class ) : string
	{
		return $this->{"image_{$image_class}"};
	}
	
	public function setImage( string $image_class, $image ) : void
	{
		$this->{"image_{$image_class}"} = $image;
	}
	
	public function getImageThumbnailUrl( string $image_class, int $max_w, int $max_h ): string
	{
		return EShop_Managers::Image()->getThumbnailUrl(
			$this->getImage( $image_class ),
			$max_w,
			$max_h
		);
	}
	
	public function getImageUrl( string $image_class ): string
	{
		return EShop_Managers::Image()->getUrl(
			$this->getImage( $image_class )
		);
	}
	
	public function defineImages(): void
	{
		$images = EShopEntity_Definition::get( $this )?->getImages();
		if(!$images) {
			return;
		}
		
		foreach($images as $image_class=>$title) {
			$this->defineImage( $image_class, Tr::_($title) );
		}
		
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
		/** @noinspection PhpInstanceofIsAlwaysTrueInspection */
		if($this instanceof EShopEntity_HasEShopRelation_Interface) {
			$eshop = $this->getEshop();
		} else {
			$eshop = null;
		}
		
		$manager = Admin_Managers::Image();
		
		$manager->defineImage(
			entity: static::getEntityType(),
			object_id: $this->getId(),
			image_class: $image_class,
			image_title: Tr::_($image_title),
			image_property_getter: function() use ( $image_class ): string {
				return $this->getImage( $image_class );
			},
			image_property_setter: function( string $val ) use ( $image_class ): void {
				$this->setImage( $image_class, $val );
				$this->save();
			},
			eshop: $eshop
		);
	}
	
	public function uploadImage(
		string $image_class,
		string $tmp_file_path,
		string $file_name
	) : void {
		
		/** @noinspection PhpInstanceofIsAlwaysTrueInspection */
		if($this instanceof EShopEntity_HasEShopRelation_Interface) {
			$eshop = $this->getEshop();
		} else {
			$eshop = null;
		}
		
		$manager = Admin_Managers::Image();
		
		$manager->uploadImage(
			tmp_file_path: $tmp_file_path,
			file_name: $file_name,
			entity: static::getEntityType(),
			object_id: $this->getId(),
			image_class: $image_class,
			eshop: $eshop
		);
	}
	
	
}