<?php
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Property_Options_Option as Application_Property_Options_Option;
use JetApplication\Shops;

#[DataModel_Definition(
	parent_model_class: Property::class
)]
class Property_Options_Option extends Application_Property_Options_Option implements Admin_Entity_Interface
{
	use Admin_Entity_Trait;
	
	public function defineImages() : void
	{
		$manager = Admin_Managers::Image();
		
		
		foreach(Shops::getList() as $shop) {
			$manager->defineImage(
				entity: 'property_option',
				object_id: $this->id,
				image_class:  'main',
				image_title:  Tr::_('Main image'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageMain();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageMain( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
			$manager->defineImage(
				entity: 'property_option',
				object_id: $this->id,
				image_class:  'pictogram',
				image_title:  Tr::_('Pictogram image'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImagePictogram();
				},
				image_property_setter: function( string $val ) use ($shop) : void {
					$this->getShopData( $shop )->setImagePictogram( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
		}
	}
}
