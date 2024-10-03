<?php

namespace JetApplicationModule\Admin\Catalog\Categories;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Category as Application_Category;

#[DataModel_Definition]
class Category extends Application_Category implements Admin_Entity_WithShopData_Interface {
	use Admin_Entity_WithShopData_Trait;
	
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function defineImages(): void
	{
		$this->defineImage(
			image_class:  'main',
			image_title:  Tr::_('Main image'),
		);
		$this->defineImage(
			image_class:  'pictogram',
			image_title:  Tr::_('Pictogram image'),
		);
	}
	

	
}