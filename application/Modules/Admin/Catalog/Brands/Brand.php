<?php
namespace JetApplicationModule\Admin\Catalog\Brands;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Brand as Application_Brand;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;

#[DataModel_Definition]
class Brand extends Application_Brand implements Admin_Entity_WithShopData_Interface
{
	
	use Admin_Entity_WithShopData_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'logo',
			image_title:  Tr::_('Logo'),
		);
		$this->defineImage(
			image_class:  'big_logo',
			image_title:  Tr::_('Big logo'),
		);
		$this->defineImage(
			image_class:  'title',
			image_title:  Tr::_('Title image'),
		);
	}
	
	
}