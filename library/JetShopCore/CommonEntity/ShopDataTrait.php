<?php
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\DataModel_PropertyFilter;
use Jet\Form;
use Jet\Mvc;
use Jet\Mvc_View;

trait Core_CommonEntity_ShopDataTrait
{

	#[DataModel_Definition(
		type: DataModel::TYPE_STRING,
		max_len: 100,
		is_id: true,
		is_key: true,
		form_field_type: false
	)]
	protected string $shop_code = '';

	#[DataModel_Definition(
		type: DataModel::TYPE_BOOL,
		is_key: true,
		form_field_label: 'Is active'
	)]
	protected bool $is_active = true;


	public function getArrayKeyValue() : null|string|int
	{
		return $this->shop_code;
	}

	public function getShopCode() : string
	{
		return $this->shop_code;
	}

	public function setShopCode( string $shop_code ) : void
	{
		$this->shop_code = $shop_code;
	}

	public function isActive() : bool
	{
		return $this->is_active;
	}

	public function setIsActive( bool $is_active ) : void
	{
		$this->is_active = $is_active;
	}

	public function renderShopDataBlock_start() : string
	{
		$view = new Mvc_View( Mvc::getCurrentSite()->getViewsPath() );

		$view->setVar('shop', Shops::get($this->getShopCode()) );

		return $view->render('shopDataBlock/start');

	}

	public function renderShopDataBlock_end() : string
	{
		$view = new Mvc_View( Mvc::getCurrentSite()->getViewsPath() );

		$view->setVar('shop', Shops::get($this->getShopCode()) );

		return $view->render('shopDataBlock/end');

	}

	public function renderImageWidgets( array $image_classes ) : string
	{
		$res = '';
		$res .= $this->renderShopDataBlock_start();

		$res .= static::renderImageWidget_container_start();
			foreach($image_classes as $image_class=>$title):
				$res .= $this->renderImageWidget( $image_class, $title );
			endforeach;
		$res .= static::renderImageWidget_container_end();

		$res .= $this->renderShopDataBlock_end();

		return $res;
	}

	public function getForm( string $form_name, array|DataModel_PropertyFilter|null $property_filter = null ) : Form
	{
		if(!Shops::exists( $this->shop_code )) {
			return new Form($form_name, []);
		}

		return parent::getForm( $form_name, $property_filter );
	}
}