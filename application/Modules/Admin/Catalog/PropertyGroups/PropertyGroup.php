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
use JetApplication\Admin_Entity_FulltextSearchIndexDataProvider_Trait;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Admin_FulltextSearch_IndexDataProvider;
use JetApplication\Admin_Managers;
use JetApplication\KindOfProduct;
use JetApplication\Shops;
use JetApplication\PropertyGroup as Application_PropertyGroup;

#[DataModel_Definition(
	force_class_name: Application_PropertyGroup::class
)]
class PropertyGroup extends Application_PropertyGroup implements Admin_Entity_Interface,Admin_FulltextSearch_IndexDataProvider {

	use Admin_Entity_Trait;
	use Admin_Entity_FulltextSearchIndexDataProvider_Trait;
	
	public function isItPossibleToDelete( array|null &$used_in_kinds_of_product=[] ) : bool
	{
		$used_in_kinds_of_product = KindOfProduct::getByPropertyGroup( $this );
		
		return count($used_in_kinds_of_product)==0;
	}
	
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	public function defineImages() : void
	{
		$manager = Admin_Managers::Image();
		
		
		foreach(Shops::getList() as $shop) {
			$manager->defineImage(
				entity: 'property_group',
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
				entity: 'property_group',
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
	
	public function handleImages( bool $editable=true ) : void
	{
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $editable );
		$manager->handleSelectImageWidgets();
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
		return $this->isActive();
	}
	
	public function getAdminFulltextObjectTitle(): string
	{
		return $this->getInternalName();
	}
	
	public function getAdminFulltextTexts(): array
	{
		return [ $this->internal_name ];
	}
}