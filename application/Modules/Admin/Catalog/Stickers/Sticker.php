<?php
namespace JetApplicationModule\Admin\Catalog\Stickers;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait_WithCode;
use JetApplication\Admin_Managers;
use JetApplication\Shops;
use JetApplication\Sticker as Application_Sticker;

#[DataModel_Definition(
	force_class_name: Application_Sticker::class
)]
class Sticker extends Application_Sticker implements Admin_Entity_Interface
{
	use Admin_Entity_Trait_WithCode;
	
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->code );
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
				entity: 'sticker',
				object_id: $this->code,
				image_class:  'pictogram_filter',
				image_title:  Tr::_('Pictogram - Filter'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageFilter();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageFilter( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
			
			$manager->defineImage(
				entity: 'sticker',
				object_id: $this->code,
				image_class:  'pictogram_product_detail',
				image_title:  Tr::_('Pictogram - Product detail'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageProductDetail();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageProductDetail( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
			
			$manager->defineImage(
				entity: 'sticker',
				object_id: $this->code,
				image_class:  'pictogram_product_listing',
				image_title:  Tr::_('Pictogram - Product listing'),
				image_property_getter: function() use ($shop) : string {
					return $this->getShopData( $shop )->getImageProductListing();
				} ,
				image_property_setter: function( string $val )  use ($shop) : void {
					$this->getShopData( $shop )->setImageProductListing( $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
			
		}
	}
}