<?php
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Property_Options_Option as Application_Property_Options_Option;

#[DataModel_Definition(
	parent_model_class: Property::class
)]
class Property_Options_Option extends Application_Property_Options_Option implements Admin_Entity_WithShopData_Interface
{
	use Admin_Entity_WithShopData_Trait;
	
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'main',
			image_title:  Tr::_('Main image')
		);
		$this->defineImage(
			image_class:  'pictogram',
			image_title:  Tr::_('Pictogram image')
		);
	}
	
	public function getEditURL(): string
	{
		return '';
	}
}
