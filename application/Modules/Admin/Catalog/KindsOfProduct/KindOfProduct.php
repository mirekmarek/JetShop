<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\KindsOfProduct;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_FulltextSearchIndexDataProvider_Trait;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Admin_FulltextSearch_IndexDataProvider;
use JetApplication\Admin_Managers;
use JetApplication\Category;
use JetApplication\KindOfProduct as Application_KindOfProduct;
use JetApplication\Product;
use JetApplication\Shops;

#[DataModel_Definition(
	force_class_name: Application_KindOfProduct::class
)]
class KindOfProduct extends Application_KindOfProduct implements Admin_Entity_Interface, Admin_FulltextSearch_IndexDataProvider
{
	use Admin_Entity_Trait;
	use Admin_Entity_FulltextSearchIndexDataProvider_Trait;
	
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function isItPossibleToDelete( array|null &$used_by_products=[], array|null &$used_by_categories=[] ) : bool
	{
		$used_by_products = Product::getByKind( $this );
		$used_by_categories = Category::getByKindOfProduct( $this );
		
		return count($used_by_products)==0 && count($used_by_categories)==0;
	}
	
	
	public function defineImages() : void
	{
		$manager = Admin_Managers::Image();
		
		
		foreach(Shops::getList() as $shop) {
			$manager->defineImage(
				entity: 'kind_of_product',
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
				entity: 'kind_of_product',
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
	
	public function actualizeAutoAppend() : void
	{
	}
	
	
	public function getAdminFulltextObjectClass(): string
	{
		return static::getEntityType();
	}
	
	public function getAdminFulltextObjectId(): string
	{
		return $this->id;
	}
	
	public function getAdminFulltextObjectType(): string
	{
		return '';
	}
	
	public function getAdminFulltextObjectIsActive(): bool
	{
		return $this->is_active;
	}
	
	public function getAdminFulltextObjectTitle(): string
	{
		return $this->internal_name;
	}
	
	public function getAdminFulltextTexts(): array
	{
		return [$this->internal_name];
	}
}