<?php
namespace JetApplicationModule\Admin\Catalog\Brands;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Brand as Application_Brand;
use JetApplication\Shops;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait;

#[DataModel_Definition(
	force_class_name: Application_Brand::class
)]
class Brand extends Application_Brand implements Admin_Entity_Interface {
	
	use Admin_Entity_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	
	public function afterAdd() : void
	{
	}
	
	public function afterUpdate() : void
	{
	}
	
	public function afterDelete() : void
	{
	}
	
	
	
	public function defineImages() : void
	{
		$manager = Admin_Managers::Image();
		
		foreach(Shops::getList() as $shop) {
			$manager->defineImage(
				entity: 'brand',
				object_id: $this->id,
				image_class:  'logo',
				image_title:  Tr::_('Logo'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageLogo();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageLogo( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
			
			$manager->defineImage(
				entity: 'brand',
				object_id: $this->id,
				image_class:  'big_logo',
				image_title:  Tr::_('Big logo'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageBigLogo();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageBigLogo( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
			
			$manager->defineImage(
				entity: 'brand',
				object_id: $this->id,
				image_class:  'title',
				image_title:  Tr::_('Title image'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageTitle();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageTitle( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
		}
	}
	
	
}