<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\PropertyGroups;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\PropertyGroup as Application_PropertyGroup;

#[DataModel_Definition]
class PropertyGroup extends Application_PropertyGroup implements Admin_Entity_WithShopData_Interface {

	use Admin_Entity_WithShopData_Trait;
	
	public function isItPossibleToDelete() : bool
	{
		//TODO:
		return true;
		//$used_in_kinds_of_product = KindOfProduct::getByPropertyGroup( $this );
		//return count($used_in_kinds_of_product)==0;
	}
	
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
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
}