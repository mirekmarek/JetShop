<?php
namespace JetShop;

use Jet\Form;
use Jet\Mvc;
use Jet\Mvc_View;
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
		if(!$this->getPossibleToEditImages()) {
			return null;
		}

		if(!isset($this->image_upload_forms[$image_class])) {
			$this->image_upload_forms[$image_class] = Images::generateUploadForm($this->getImageEntity(), $image_class, $this->shop_id);
		}

		return $this->image_upload_forms[$image_class];

	}

	public function catchImageUploadForm( string $image_class ) : bool
	{
		if(!$this->getPossibleToEditImages()) {
			return false;
		}

		$form = $this->getImageUploadForm( $image_class );
		if(!$form) {
			return false;
		}

		$property_name = 'image_'.$image_class;

		return Images::catchUploadForm(
			$form,
			$this->getImageEntity(),
			$image_class,
			$this->getShopId(),
			$this->getImageObjectId(),
			$this->{$property_name}
		);

	}

	public function getImageDeleteForm( string $image_class ) : Form|null
	{
		if(!$this->getPossibleToEditImages()) {
			return null;
		}

		if(!isset($this->image_delete_forms[$image_class])) {

			$form = new Form('image_delete_'.$this->getImageEntity().'_'.$image_class.'_'.$this->shop_id, []);

			$this->image_delete_forms[$image_class] = $form;
		}

		return $this->image_delete_forms[$image_class];

	}

	public function catchImageDeleteForm( string $image_class ) : bool
	{
		if(!$this->getPossibleToEditImages()) {
			return false;
		}

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

	protected static function renderImageWidget_view() : Mvc_View
	{
		return new Mvc_View( Mvc::getCurrentSite()->getViewsPath() );
	}

	public static function renderImageWidget_container_start() : string
	{
		$view = static::renderImageWidget_view();

		return $view->render('imageWidget/container/start');
	}

	public static function renderImageWidget_container_end() : string
	{
		$view = static::renderImageWidget_view();

		return $view->render('imageWidget/container/end');
	}

	public function renderImageWidget( string $image_class, string $title ) : string
	{
		$view = static::renderImageWidget_view();

		$view->setVar('image_class', $image_class);
		$view->setVar('title', $title );
		$view->setVar('shop_id', $this->getShopId() );
		$view->setVar('shop_data', $this );

		return $view->render('imageWidget');
	}

	public function renderImageWidget_Image( string $image_class ) : string
	{
		$view = static::renderImageWidget_view();

		$view->setVar('image_class', $image_class);
		$view->setVar('shop_id', $this->getShopId() );
		$view->setVar('shop_data', $this );

		return $view->render('imageWidget/image');
	}

	public function catchImageWidget(
		$image_class,
		callable $onUpload,
		callable $onDelete
	) : void
	{
		if(!$this->getPossibleToEditImages()) {
			return;
		}

		$ok = null;
		if($this->getImageUploadForm( $image_class )->catchInput()) {
			$ok = false;
			if( $this->catchImageUploadForm( $image_class ) ) {
				$onUpload();

				$ok = true;
			}

		}

		if( $this->catchImageDeleteForm( $image_class ) ) {
			$onDelete();
			$ok = true;
		}

		if($ok!==null) {
			$entity = $this->getImageEntity();
			$shop_id = $this->getShopId();

			AJAX::formResponse($ok, [
				'image_'.$entity.'_'.$image_class.'_'.$shop_id => $this->renderImageWidget_Image( $image_class ),
				'system-messages-area' => '',
			]);
		}

	}

}