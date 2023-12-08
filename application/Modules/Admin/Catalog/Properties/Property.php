<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Tr;
use JetApplication\Admin_Entity_Interface;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Admin_FulltextSearch_IndexDataProvider;
use JetApplication\Admin_Entity_FulltextSearchIndexDataProvider_Trait;
use JetApplication\Admin_Managers;
use JetApplication\KindOfProduct;
use JetApplication\Shops;
use JetApplication\Property as Application_Property;


abstract class Property extends Application_Property implements Admin_Entity_Interface,Admin_FulltextSearch_IndexDataProvider {
	use Admin_Entity_Trait;
	use Admin_Entity_FulltextSearchIndexDataProvider_Trait;
	
	
	protected static array $types = [
		self::PROPERTY_TYPE_NUMBER => 'Number',
		self::PROPERTY_TYPE_BOOL => 'Yes / No',
		self::PROPERTY_TYPE_OPTIONS => 'Options',
	];
	
	
	public static function getTypes() : array
	{
		$list = [];
		
		foreach( self::$types as $type=>$label ) {
			$list[$type] = Tr::_($label);
		}
		
		return $list;
	}
	
	public function getTypeTitle() : string
	{
		return static::getTypes()[$this->getType()];
	}
	
	
	public function isItPossibleToDelete( array|null &$used_in_kinds_of_product=[] ) : bool
	{
		$used_in_kinds_of_product = KindOfProduct::getByProperty( $this );
		
		return count($used_in_kinds_of_product)==0;
	}
	
	public function getAddForm() : Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->generateAddForm();
		}
		
		return $this->_add_form;
	}
	
	protected function generateAddForm() : Form
	{
		$form = $this->createForm('property_add_form');
		
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
		}
		
		return $form;
	}
	
	public function getEditForm() : Form
	{
		if(!$this->_edit_form) {
			$this->_edit_form = $this->generateEditForm();
		}
		
		return $this->_edit_form;
	}
	
	protected function generateEditForm() : Form
	{
		$form = $this->createForm('property_edit_form_'.$this->id);
		
		foreach( Shops::getList() as $shop ) {
			$shop_key = $shop->getKey();
		}
		
		return $form;
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
				entity: 'property',
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
				entity: 'property',
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
		return $this->type;
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