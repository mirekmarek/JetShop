<?php
namespace JetApplicationModule\Admin\Catalog\Stickers;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Sticker as Application_Sticker;

#[DataModel_Definition]
class Sticker extends Application_Sticker implements Admin_Entity_WithShopData_Interface
{
	use Admin_Entity_WithShopData_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'pictogram_filter',
			image_title:  Tr::_('Pictogram - Filter'),
		);
		
		$this->defineImage(
			image_class:  'pictogram_product_detail',
			image_title:  Tr::_('Pictogram - Product detail'),
		);
		
		$this->defineImage(
			image_class:  'pictogram_product_listing',
			image_title:  Tr::_('Pictogram - Product listing'),
		);
		
	}
	
	public function isItPossibleToDelete() : bool
	{
		return true;
	}
}