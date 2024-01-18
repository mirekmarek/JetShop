<?php
namespace JetApplication;

use Jet\DataModel_Definition_Model;
use Jet\DataModel_Definition_Property_DataModel;

trait Admin_Entity_WithShopData_Trait {
	use Admin_Entity_Common_Trait;
	
	
	public function handleImages() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $this->isEditable() );
		$manager->handleSelectImageWidgets();
	}
	
	
	protected function defineImage( string $image_class, string $image_title ) : void
	{
		$manager = Admin_Managers::Image();
		
		foreach(Shops::getList() as $shop) {
			$manager->defineImage(
				entity: static::getEntityType(),
				object_id: $this->id,
				image_class: $image_class,
				image_title: $image_title,
				image_property_getter: function() use ( $shop, $image_class ): string {
					return $this->getShopData( $shop )->getImage( $image_class );
				},
				image_property_setter: function( string $val ) use ( $shop, $image_class ): void {
					$this->getShopData( $shop )->setImage( $image_class, $val );
					$this->getShopData( $shop )->save();
				},
				shop: $shop
			);
		}
	}
	
	public static function getEntityShopDataInstance() : Entity_WithShopData_ShopData
	{
		/**
		 * @var DataModel_Definition_Model $def
		 */
		$def = static::getDataModelDefinition();
		
		/**
		 * @var DataModel_Definition_Property_DataModel $prop
		 */
		$prop = $def->getProperty('shop_data');
		
		$class = $prop->getValueDataModelClass();
		
		return new $class();
	}
}