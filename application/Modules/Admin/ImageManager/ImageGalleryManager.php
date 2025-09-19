<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ImageManager;


use Jet\AJAX;
use Jet\Form;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Form_Field_FileImage;
use JetApplication\Application_Admin;
use JetApplication\ImageGallery;

class ImageGalleryManager
{
	protected ImageGallery $gallery;
	protected MVC_View $view;
	protected bool $editable;
	protected Image $defined_image;
	
	public function __construct( ImageGallery $gallery, MVC_View $view, bool $editable )
	{
		$this->gallery = $gallery;
		$this->view = $view;
		$this->editable = $editable;
		
		$this->defined_image = new Image(
			entity: $this->gallery->getEntityType(),
			object_id: $this->gallery->getEntityId(),
			image_property_setter: function( $value ) {
				$this->gallery->addImage( $value );
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
			$this->view->setVar( 'item', $this->gallery );
			
			$updated = false;
			switch( $GET->getString( 'image_gallery_action' ) ) {
				case 'upload_image':
					Application_Admin::handleUploadTooLarge();
					
					$this->defined_image->catchUploadForm();
					
					$updated = true;
					break;
				case 'delete_images':

					foreach( explode( ',', $GET->getString( 'images' ) ) as $image_id ) {
						$image = $this->gallery->getImageById( $image_id );
						if( !$image ) {
							continue;
						}
						$image_file = $image->getImageFile();
						
						$image_def = new Image(
							entity: $this->gallery->getEntityType(),
							object_id: $this->gallery->getEntityId(),
							image_property_getter: function() use ($image_file) {
								return $image_file;
							},
							image_property_setter: function( $image ) use ($image_id) {
								$this->gallery->deleteImage( $image_id );
							}
						);
						
						$image_def->delete();

					}

					$updated = true;
					break;
				case 'sort_images':
					$this->gallery->sortImages( explode( ',', $GET->getString( 'images' ) ) );
					$updated = true;
					break;
			}
			
			if( $updated ) {
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
		
		$this->view->setVar( 'item', $this->gallery );
		$this->view->setVar( 'editable', $this->editable );
		$this->view->setVar( 'upload_form', $this->getUploadForm() );
		
		return $this->view->render( 'image-gallery-management' );
		
	}
}