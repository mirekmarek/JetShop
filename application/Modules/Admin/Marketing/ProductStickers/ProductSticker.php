<?php
namespace JetApplicationModule\Admin\Marketing\ProductStickers;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_ProductSticker;

#[DataModel_Definition]
class ProductSticker extends Marketing_ProductSticker implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'pictogram_product_detail',
			image_title:  Tr::_('Pictogram - Product detail'),
		);
		
		$this->defineImage(
			image_class:  'pictogram_product_listing',
			image_title:  Tr::_('Pictogram - Product listing'),
		);
	}
	
}