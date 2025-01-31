<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\ImageManager;


use Jet\AJAX;
use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\MVC_View;
use Jet\Translator;
use JetApplication\Admin_Managers_Image;

use JetApplication\EShopEntity_HasImageGallery_Interface;
use JetApplication\ImageGallery_Image;
use JetApplication\EShop;


class Main extends Application_Module implements Admin_Managers_Image
{
	
	/**
	 * @var Image[]
	 */
	protected array $defined_images = [];
	
	protected bool $editable = false;
	
	protected bool $eshop_sync_mode = true;
	
	
	public function getEshopSyncMode(): bool
	{
		return $this->eshop_sync_mode;
	}
	
	public function setEshopSyncMode( bool $eshop_sync_mode ): void
	{
		$this->eshop_sync_mode = $eshop_sync_mode;
	}
	
	
	public function resetDefinedImages() : void
	{
		$this->defined_images = [];
	}
	
	public function defineImage(
		string      $entity,
		string|int  $object_id,
		?string     $image_class = '',
		?string     $image_title = '',
		?callable   $image_property_getter = null,
		?callable   $image_property_setter = null,
		?EShop $eshop = null
	): void
	{
		$image = new Image(
			$entity,
			$object_id,
			$image_class,
			$image_title,
			$image_property_getter,
			$image_property_setter,
			$eshop
		);
		
		$this->defined_images[$image->getKey()] = $image;
	}
	
	public function uploadImage(
		string $tmp_file_path,
		string $file_name,
		string $entity,
		string|int $object_id,
		string $image_class,
		?EShop $eshop = null
	) : void
	{
		$this->defined_images[
			Image::generateKey($entity, $object_id, $image_class, $eshop)
		]->upload( $tmp_file_path, $file_name );
	}
	
	
	public function getEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = $editable;
	}
	
	
	public function handleSelectImageWidgets(): bool
	{
		if( !$this->editable ) {
			return false;
		}
		
		foreach( $this->defined_images as $image ) {
			if( $image->catchUploadForm() !== null ) {
				if( $this->getEshopSyncMode() ) {
					$this->cloneImageToOtherShops( $image );
				}
				
				AJAX::operationResponse( true, [
					$image->getHTMLElementId() => $this->renderImageWidget_Image( $image ),
				] );
				
				return true;
			}
			if( $image->catchImageDeleteForm() !== null ) {
				if( $this->getEshopSyncMode() ) {
					$this->cloneImageToOtherShops( $image );
				}
				
				AJAX::operationResponse( true, [
					$image->getHTMLElementId() => $this->renderImageWidget_Image( $image ),
				] );
				
				return true;
			}
		}
		
		return false;
	}
	
	public function cloneImageToOtherShops( Image $source_image ): void
	{
		if( !$source_image->getEshop() ) {
			return;
		}
		
		foreach( $this->defined_images as $image ) {
			if(
				$source_image->getImageClass() != $image->getImageClass() ||
				$source_image->getObjectId() != $image->getObjectId()
			) {
				continue;
			}
			
			if( $source_image->getEshop()->getKey() != $image->getEshop()->getKey() ) {
				$image->setImage( $source_image->getImage() );
			}
			
		}
	}
	
	
	protected function initView(): MVC_View
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		return $view;
	}
	
	
	protected function renderImageWidget( Image $image ): string
	{
		$view = $this->initView();
		$view->setVar( 'image', $image );
		$view->setVar( 'editable', $this->getEditable() );
		
		return $view->render( 'image-widget' );
	}
	
	protected function renderImageWidget_Image( Image $image ): string
	{
		$view = static::initView();
		
		$view->setVar( 'image', $image );
		$view->setVar( 'editable', $this->getEditable() );
		
		return $view->render( 'image-widget/image' );
	}
	
	
	public function renderImageWidgets( ?EShop $eshop = null ): string
	{
		
		$res = '';
		Translator::setCurrentDictionaryTemporary( $this->module_manifest->getName(), function() use ( $eshop, &$res ) {
			
			if( $eshop ) {
				foreach( $this->defined_images as $image ):
					if( $image->getEshop()->getKey() == $eshop->getKey() ) {
						$res .= $this->renderImageWidget( $image );
					}
				endforeach;
			} else {
				foreach( $this->defined_images as $image ):
					$res .= $this->renderImageWidget( $image );
				endforeach;
			}
			
		} );
		
		
		return $res;
	}
	
	public function renderMain(): string
	{
		$res = '';
		Translator::setCurrentDictionaryTemporary( $this->module_manifest->getName(), function() use ( &$res ) {
			$view = $this->initView();
			$res = $view->render( 'main' );
		} );
		
		return $res;
	}
	
	public function renderStandardManagement(): string
	{
		$view = $this->initView();
		$view->setVar( 'manager', $this );
		$res = $view->render( 'standard-management' );
		
		return $res;
	}
	
	
	protected ImageGalleryManager $image_gallery_manager;
	
	
	public function handleImageGalleryManagement( EShopEntity_HasImageGallery_Interface $item ): void
	{
		$this->image_gallery_manager = new ImageGalleryManager( $item, $this->initView(), $this->editable );
		if($this->editable) {
			$this->image_gallery_manager->handle();
		}
		
	}
	
	public function uploadImageGallery( EShopEntity_HasImageGallery_Interface $item, array $images ) : void
	{
		$def = new Image(
			entity: $item->getEntityType(),
			object_id: $item->getId(),
			image_property_setter: function( $value ) use ($item) {
				$item->addImage( $value );
				$item->save();
			}
		);
		
		foreach($images as $image=>$name) {
			$def->upload( $image, $name );
		}
	}
	
	public function renderImageGalleryManagement(): string
	{
		return Translator::setCurrentDictionaryTemporary( $this->module_manifest->getName(), function() {
			return $this->image_gallery_manager->render();
		} );
		
		
		return $res;
	}
	
	public function getImageGalleryImageURL( ImageGallery_Image $image ): string
	{
		$this->image_gallery_manager->getDefinedImage()->setImagePropertyGetter( function() use ( $image ) {
			return $image->getImageFile();
		} );
		
		return $this->image_gallery_manager->getDefinedImage()->getUrl();
	}
	
	
	public function getImageGalleryImageThumbnailUrl( ImageGallery_Image $image, int $max_w, int $max_h ): string
	{
		$this->image_gallery_manager->getDefinedImage()->setImagePropertyGetter( function() use ( $image ) {
			return $image->getImageFile();
		} );
		
		return $this->image_gallery_manager->getDefinedImage()->getThumbnailUrl( $max_w, $max_h );
	}
	
	
	public function commonImageManager( string $entity, int $entity_id ): string
	{
		$manager = new CommonImageManager($entity, $entity_id, $this->initView());
		return $manager->handle();
	}
	
}