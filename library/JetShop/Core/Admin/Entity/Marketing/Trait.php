<?php
namespace JetShop;

use Jet\Form;
use JetApplication\Admin_Entity_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;


trait Core_Admin_Entity_Marketing_Trait {
	use Admin_Entity_Trait;
	
	protected function setupForm( Form $form ) : void
	{
	}
	
	public function setupAddForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	public function setupEditForm( Form $form ): void
	{
		$this->setupForm( $form );
	}
	
	public function hasImages() : bool
	{
		return true;
	}
	
	public function handleImages() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->defineImages();
		
		$manager = Admin_Managers::Image();
		$manager->setEditable( $this->isEditable() );
		$manager->handleSelectImageWidgets();
	}
	
	
	public function defineImage( string $image_class, string $image_title ) : void
	{
		$manager = Admin_Managers::Image();
		
		$manager->defineImage(
			entity: static::getEntityType(),
			object_id: $this->id,
			image_class: $image_class,
			image_title: $image_title,
			image_property_getter: function() use ( $image_class ): string {
				return $this->getImage( $image_class );
			},
			image_property_setter: function( string $val ) use ( $image_class ): void {
				$this->setImage( $image_class, $val );
				$this->save();
			},
			eshop: $this->getEshop()
		);
	}
	
}