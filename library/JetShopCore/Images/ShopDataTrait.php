<?php
namespace JetShop;

use Jet\Form;
use Jet\Logger;
use Jet\MVC;
use Jet\MVC_View;
use Jet\AJAX;

trait Core_Images_ShopDataTrait {
	/**
	 * @var Form[]
	 */
	protected array $image_upload_forms = [];

	/**
	 * @var Form[]
	 */
	protected array $image_delete_forms = [];

	public function getImage( string $image_class ) : string
	{
		$property_name = 'image_'.$image_class;

		return $this->{$property_name};
	}

	public function setImage( string $image_class, string $path ) : void
	{
		$property_name = 'image_'.$image_class;

		$this->{$property_name} = $path;
	}

	public function getImageUrl( string $image_class ) : string
	{
		return Images::getUrl( $this->getImage( $image_class ) );
	}

	public function getImageThumbnailUrl( string $image_class, int $max_w, int $max_h ) : string
	{
		return Images::getThumbnailUrl( $this->getImage( $image_class ), $max_w, $max_h );
	}

	public function getImageUploadForm( string $image_class ) : Form|null
	{
		if(!isset($this->image_upload_forms[$image_class])) {
			$this->image_upload_forms[$image_class] = Images::generateUploadForm($this->getImageEntity(), $image_class, $this->getShop() );
		}

		return $this->image_upload_forms[$image_class];

	}

	public function catchImageUploadForm( string $image_class ) : bool
	{
		$form = $this->getImageUploadForm( $image_class );
		if(!$form) {
			return false;
		}

		$property_name = 'image_'.$image_class;

		return Images::catchUploadForm(
			$form,
			$this->getImageEntity(),
			$image_class,
			$this->getShop(),
			$this->getImageObjectId(),
			$this->{$property_name}
		);

	}

	public function getImageDeleteForm( string $image_class ) : Form|null
	{
		if(!isset($this->image_delete_forms[$image_class])) {

			$form = new Form('image_delete_'.$this->getImageEntity().'_'.$image_class.'_'.$this->getShopKey(), []);

			$this->image_delete_forms[$image_class] = $form;
		}

		return $this->image_delete_forms[$image_class];

	}

	public function catchImageDeleteForm( string $image_class ) : bool
	{
		$form = $this->getImageDeleteForm( $image_class );
		if(!$form) {
			return false;
		}

		if(
			!$form->catchInput() ||
			!$form->validate()
		) {
			return false;
		}

		Images::deleteImage(
			$this->getImage($image_class)
		);

		$this->setImage($image_class, '');

		return true;
	}

	protected static function renderImageWidget_view() : MVC_View
	{
		return new MVC_View( MVC::getBase()->getViewsPath() );
	}

	public static function renderImageWidget_container_start() : string
	{
		$view = static::renderImageWidget_view();

		return $view->render('image-widget/container/start');
	}

	public static function renderImageWidget_container_end() : string
	{
		$view = static::renderImageWidget_view();

		return $view->render('image-widget/container/end');
	}

	public function renderImageWidget( string $image_class, string $title, bool $editable ) : string
	{
		$view = static::renderImageWidget_view();

		$view->setVar('image_class', $image_class);
		$view->setVar('title', $title );
		$view->setVar('shop', $this->getShop() );
		$view->setVar('shop_data', $this );
		$view->setVar('editable', $editable );

		return $view->render('image-widget');
	}

	public function renderImageWidget_Image( string $image_class, bool $editable ) : string
	{
		$view = static::renderImageWidget_view();

		$view->setVar('image_class', $image_class);
		$view->setVar('shop', $this->getShop() );
		$view->setVar('shop_data', $this );
		$view->setVar('editable', $editable );

		return $view->render('image-widget/image');
	}

	public function catchImageWidget(
		Shops_Shop $shop,
		string $entity_name,
		string $object_id,
		string $object_name,
		string $upload_event,
		string $delete_event
	) : void
	{
		foreach( static::getImageClasses() as $image_class=>$image_class_name ) {

			$ok = null;
			if($this->getImageUploadForm( $image_class )->catchInput()) {
				if( $this->catchImageUploadForm( $image_class ) ) {
					$this->save();

					Logger::success(
						event: $upload_event,
						event_message: $entity_name.' \''.$object_name.'\' ('.$object_id.') image '.$image_class.' uploaded',
						context_object_id: $object_id,
						context_object_name: $object_name,
						context_object_data: [
							'image_class' => $image_class,
							'shop_key' => $shop->getKey()
						]
					);

					$ok = true;
				} else {
					$ok = false;
				}

			}

			if( $this->catchImageDeleteForm( $image_class ) ) {
				$this->save();

				Logger::success(
					event: $delete_event,
					event_message: $entity_name.' \''.$object_name.'\' ('.$object_id.') image '.$image_class.' deleted',
					context_object_id: $object_id,
					context_object_name: $object_name,
					context_object_data: [
						'image_class' => $image_class,
						'shop_key' => $shop->getKey()
					]
				);

				$ok = true;
			}

			if($ok!==null) {
				$entity = $this->getImageEntity();

				AJAX::operationResponse($ok, [
					'image_'.$entity.'_'.$image_class.'_'.$shop->getKey() => $this->renderImageWidget_Image( $image_class, true ),
					'system-messages-area' => '',
				]);
			}
		}
	}

	public function renderImageWidgets( array $image_classes, bool $editable ) : string
	{
		$res = $this->renderShopDataBlock_start();

		$res .= static::renderImageWidget_container_start();
		foreach($image_classes as $image_class=>$title):
			$res .= $this->renderImageWidget( $image_class, $title, $editable );
		endforeach;
		$res .= static::renderImageWidget_container_end();

		$res .= $this->renderShopDataBlock_end();

		return $res;
	}

}