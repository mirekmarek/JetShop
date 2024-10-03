<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\ImageManager;

use Jet\AJAX;
use Jet\Form;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Form_Field_FileImage;
use JetApplication\Application_Admin;
use JetApplication\Product;

class ProductImageManager
{
	protected Product $product;
	protected MVC_View $view;
	protected bool $editable;
	protected Image $product_defined_image;
	
	public function __construct( Product $product, MVC_View $view, bool $editable )
	{
		$this->product = $product;
		$this->view = $view;
		$this->editable = $editable;
		
		$this->product_defined_image = new Image(
			entity: $this->product->getEntityType(),
			object_id: $this->product->getId(),
			image_property_setter: function( $value ) {
				$this->product->addImage( $value );
				$this->product->save();
			}
		);
		
		
	}
	
	public function getProductDefinedImage(): Image
	{
		return $this->product_defined_image;
	}
	
	
	
	protected function getUploadForm() : Form
	{
		$form = $this->product_defined_image->getUploadForm();
		$form->setAction( Http_Request::currentURI( ['action' => 'upload_image'] ) );
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
		if( $GET->exists( 'action' ) ) {
			
			$this->view->setVar( 'editable', $this->editable );
			$this->view->setVar( 'product', $this->product );
			
			$updated = false;
			switch( $GET->getString( 'action' ) ) {
				case 'upload_image':
					Application_Admin::handleUploadTooLarge();
					
					$this->product_defined_image->catchUploadForm();
					
					$updated = true;
					break;
				case 'delete_images':

					foreach( explode( ',', $GET->getString( 'images' ) ) as $image_id ) {
						$image = $this->product->getImageById( $image_id );
						if( !$image ) {
							continue;
						}
						$image_file = $image->getImageFile();
						
						$image_def = new Image(
							entity: $this->product->getEntityType(),
							object_id: $this->product->getId(),
							image_property_getter: function() use ($image_file) {
								return $image_file;
							},
							image_property_setter: function( $image ) use ($image_id) {
								$this->product->deleteImage( $image_id );
							}
						);
						
						$image_def->delete();

					}

					$updated = true;
					break;
				case 'sort_images':
					$this->product->sortImages( explode( ',', $GET->getString( 'images' ) ) );
					$updated = true;
					break;
			}
			
			if( $updated ) {
				$this->product->save();
				
				AJAX::commonResponse(
					[
						'result'   => 'ok',
						'snippets' => [
							'images_list' => $this->view->render( 'product-images-management/list' )
						]
					
					]
				);
				
			}
		}
		
	}
	
	public function render() : string
	{
		
		$this->view->setVar( 'product', $this->product );
		$this->view->setVar( 'editable', $this->editable );
		$this->view->setVar( 'upload_form', $this->getUploadForm() );
		
		return $this->view->render( 'product-images-management' );
		
	}
}