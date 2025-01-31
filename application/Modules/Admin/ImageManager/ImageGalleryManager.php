<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\ImageManager;


use Jet\AJAX;
use Jet\Form;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Form_Field_FileImage;
use JetApplication\Application_Admin;
use JetApplication\EShopEntity_HasImageGallery_Interface;

class ImageGalleryManager
{
	protected EShopEntity_HasImageGallery_Interface $item;
	protected MVC_View $view;
	protected bool $editable;
	protected Image $defined_image;
	
	public function __construct( EShopEntity_HasImageGallery_Interface $item, MVC_View $view, bool $editable )
	{
		$this->item = $item;
		$this->view = $view;
		$this->editable = $editable;
		
		$this->defined_image = new Image(
			entity: $this->item->getEntityIdForImageGallery(),
			object_id: $this->item->getEntityIdForImageGallery(),
			image_property_setter: function( $value ) {
				$this->item->addImage( $value );
				$this->item->save();
			}
		);
		
		
	}
	
	public function getDefinedImage(): Image
	{
		return $this->defined_image;
	}
	
	
	
	protected function getUploadForm() : Form
	{
		$form = $this->defined_image->getUploadForm();
		$form->setAction( Http_Request::currentURI( ['image_gallery_action' => 'upload_image'] ) );
		/**
		 * @var Form_Field_FileImage $img_field
		 */
		$img_field = $form->getField( 'image' );
		$img_field->setAllowMultipleUpload( true );
		
		return $form;
	}
	
	public function handle(): void
	{
		if(!$this->editable) {
			return;
		}
		
		
		$GET = Http_Request::GET();
		if( $GET->exists( 'image_gallery_action' ) ) {
			
			$this->view->setVar( 'editable', $this->editable );
			$this->view->setVar( 'item', $this->item );
			
			$updated = false;
			switch( $GET->getString( 'image_gallery_action' ) ) {
				case 'upload_image':
					Application_Admin::handleUploadTooLarge();
					
					$this->defined_image->catchUploadForm();
					
					$updated = true;
					break;
				case 'delete_images':

					foreach( explode( ',', $GET->getString( 'images' ) ) as $image_id ) {
						$image = $this->item->getImageById( $image_id );
						if( !$image ) {
							continue;
						}
						$image_file = $image->getImageFile();
						
						$image_def = new Image(
							entity: $this->item->getEntityType(),
							object_id: $this->item->getId(),
							image_property_getter: function() use ($image_file) {
								return $image_file;
							},
							image_property_setter: function( $image ) use ($image_id) {
								$this->item->deleteImage( $image_id );
							}
						);
						
						$image_def->delete();

					}

					$updated = true;
					break;
				case 'sort_images':
					$this->item->sortImages( explode( ',', $GET->getString( 'images' ) ) );
					$updated = true;
					break;
			}
			
			if( $updated ) {
				$this->item->save();
				
				AJAX::commonResponse(
					[
						'result'   => 'ok',
						'snippets' => [
							'images_list' => $this->view->render( 'image-gallery-management/list' )
						]
					
					]
				);
				
			}
		}
		
	}
	
	public function render() : string
	{
		
		$this->view->setVar( 'item', $this->item );
		$this->view->setVar( 'editable', $this->editable );
		$this->view->setVar( 'upload_form', $this->getUploadForm() );
		
		return $this->view->render( 'image-gallery-management' );
		
	}
}