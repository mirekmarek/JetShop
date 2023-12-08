<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\ImageManager;

use Jet\AJAX;
use Jet\Application_Module;
use Jet\Factory_MVC;
use Jet\Form_Field_FileImage;
use Jet\Http_Request;
use Jet\MVC_View;
use Jet\Translator;
use JetApplication\Admin_Managers_Image;

use JetApplication\Application_Admin;
use JetApplication\Product;
use JetApplication\Product_Image;
use JetApplication\Shops_Shop;

/**
 *
 */
class Main extends Application_Module implements Admin_Managers_Image
{
	public const SHOP_SYNC_MODE = true;
	
	
	/**
	 * @var Image[]
	 */
	protected array $defined_images = [];
	
	protected bool $editable = false;
	
	protected bool $shop_sync_mode = true;
	
	
	public function getShopSyncMode(): bool
	{
		return $this->shop_sync_mode;
	}
	
	public function setShopSyncMode( bool $shop_sync_mode ): void
	{
		$this->shop_sync_mode = $shop_sync_mode;
	}
	
	
	
	public function defineImage(
		string $entity,
		string|int $object_id,
		?string $image_class='',
		?string $image_title='',
		?callable $image_property_getter=null,
		?callable $image_property_setter=null,
		?Shops_Shop $shop=null
	) : void
	{
		$image = new Image(
			$entity,
			$object_id,
			$image_class,
			$image_title,
			$image_property_getter,
			$image_property_setter,
			$shop
		);
		
		$this->defined_images[ $image->getKey()] = $image;
	}
	
	
	public function getEditable(): bool
	{
		return $this->editable;
	}
	
	public function setEditable( bool $editable ): void
	{
		$this->editable = $editable;
	}
	
	
	
	public function handleSelectImageWidgets() : bool
	{
		if(!$this->editable) {
			return false;
		}
		
		foreach($this->defined_images as $image) {
			if($image->catchUploadForm()!==null) {
				if(static::SHOP_SYNC_MODE) {
					$this->cloneImageToOtherShops( $image );
				}
				
				AJAX::operationResponse(true, [
					$image->getHTMLElementId() => $this->renderImageWidget_Image( $image ),
				]);
				
				return true;
			}
			if($image->catchImageDeleteForm()!==null) {
				if(static::SHOP_SYNC_MODE) {
					$this->cloneImageToOtherShops( $image );
				}
				
				AJAX::operationResponse(true, [
					$image->getHTMLElementId() => $this->renderImageWidget_Image( $image ),
				]);
				
				return true;
			}
		}
		
		return false;
	}
	
	public function cloneImageToOtherShops( Image $source_image ) : void
	{
		if(!$source_image->getShop()) {
			return;
		}
		
		foreach($this->defined_images as $image) {
			if(
				$source_image->getImageClass()!=$image->getImageClass() ||
				$source_image->getObjectId()!=$image->getObjectId()
			) {
				continue;
			}
			
			if($source_image->getShop()->getKey()!=$image->getShop()->getKey()) {
				$image->setImage( $source_image->getImage() );
			}
			
		}
	}
	
	
	
	
	protected function initView() : MVC_View
	{
		$view = Factory_MVC::getViewInstance( $this->getViewsDir() );
		
		return $view;
	}
	
	
	protected function renderImageWidget( Image $image ) : string
	{
		$view = $this->initView();
		$view->setVar( 'image', $image);
		$view->setVar( 'editable', $this->getEditable() );
		
		return $view->render('image-widget');
	}
	
	protected function renderImageWidget_Image( Image $image ) : string
	{
		$view = static::initView();
		
		$view->setVar('image', $image);
		$view->setVar( 'editable', $this->getEditable() );
		
		return $view->render('image-widget/image');
	}
	
	
	public function renderImageWidgets( ?Shops_Shop $shop=null ) : string
	{
		
		$res = '';
		Translator::setCurrentDictionaryTemporary( $this->module_manifest->getName(), function() use ($shop, &$res) {
			
			if($shop) {
				foreach($this->defined_images as $image):
					if($image->getShop()->getKey()==$shop->getKey()) {
						$res .= $this->renderImageWidget( $image );
					}
				endforeach;
			} else {
				foreach($this->defined_images as $image):
					$res .= $this->renderImageWidget( $image );
				endforeach;
			}
			
		});
		
		
		
		return $res;
	}
	
	public function renderMain() : string
	{
		$res = '';
		Translator::setCurrentDictionaryTemporary( $this->module_manifest->getName(), function() use (&$res) {
			$view = $this->initView();
			$res = $view->render('main');
		});
		
		return $res;
	}
	
	public function renderStandardManagement() : string
	{
		$view = $this->initView();
		$view->setVar('manager', $this);
		$res = $view->render('standard-management');

		return $res;
	}
	
	
	protected Product $product;
	
	protected Image $product_defined_image;
	
	
	public function handleProductImageManagement(Product $product) : void
	{
		$this->product = $product;
		$product = $this->product;
		
		$this->defineImage(
			entity: $this->product->getEntityType(),
			object_id: $this->product->getId(),
			image_property_setter: function( $value ) {
				$this->product->addImage( $value );
				$this->product->save();
			}
		);
		
		$this->product_defined_image = array_values($this->defined_images)[0];
		
		if(!$this->getEditable()) {
			return;
		}
		
		$form = $this->product_defined_image->getUploadForm();
		$form->setAction( Http_Request::currentURI(['action'=>'upload']) );
		/**
		 * @var Form_Field_FileImage $img_field
		 */
		$img_field = $form->getField('image');
		$img_field->setAllowMultipleUpload( true );
		
		$GET = Http_Request::GET();
		if($GET->exists('action')) {
			
			$view = $this->initView();
			$view->setVar('editable', $this->getEditable() );
			$view->setVar('product', $product);
			
			$updated = false;
			switch($GET->getString('action')) {
				case 'upload':
					Application_Admin::handleUploadTooLarge();
					
					$this->product_defined_image->catchUploadForm();
					
					$updated = true;
					break;
				case 'delete':
					foreach(explode(',', $GET->getString('images')) as $image_key) {
						$image = $product->getImageByKey( $image_key );
						if(!$image) {
							continue;
						}
						
						$this->product_defined_image->setImagePropertyGetter( function() use ($image) {
							return $image->getImageFile();
						} );
						
						$this->product_defined_image->setImagePropertySetter( function( $value ) use ($image, $product) {
							$product->deleteImage( $image->getKey() );
						} );
						
						$this->product_defined_image->delete();
					}
					$updated = true;
					break;
				case 'save_sort':
					$product->sortImages( explode(',', $GET->getString('images')) );
					$updated = true;
					break;
			}
			
			if($updated) {
				$product->save();
				
				AJAX::commonResponse(
					[
						'result' => 'ok',
						'snippets' => [
							'images_list' => $view->render('product-images-management/list')
						]
					
					]
				);
				
			}
		}
	}
	
	public function renderProductImageManagement() : string
	{
		
		$res = '';
		Translator::setCurrentDictionaryTemporary( $this->module_manifest->getName(), function() use (&$res) {
			$view = $this->initView();
			$view->setVar('product', $this->product);
			$view->setVar('editable', $this->getEditable() );
			$view->setVar('upload_form', $this->product_defined_image->getUploadForm());
			$res = $view->render('product-images-management');
			
		});
		
		
		return $res;
	}
	
	public function getProductImageURL( Product_Image $image ) : string
	{
		$this->product_defined_image->setImagePropertyGetter( function() use ($image) {
			return $image->getImageFile();
		} );
		
		return $this->product_defined_image->getUrl();
	}
	
	
	public function getProductImageThumbnailUrl( Product_Image $image, int $max_w, int $max_h  ) : string
	{
		$this->product_defined_image->setImagePropertyGetter( function() use ($image) {
			return $image->getImageFile();
		} );
		
		return $this->product_defined_image->getThumbnailUrl( $max_w, $max_h );
	}
	
}