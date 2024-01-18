<?php

namespace JetApplicationModule\Admin\Catalog\Categories;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Admin_Managers;
use JetApplication\Category as Application_Category;

#[DataModel_Definition]
class Category extends Application_Category implements Admin_Entity_WithShopData_Interface {
	use Admin_Entity_WithShopData_Trait;
	
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	
	public function defineImages(): void
	{
		$this->defineImage(
			image_class:  'main',
			image_title:  Tr::_('Main image'),
		);
		$this->defineImage(
			image_class:  'pictogram',
			image_title:  Tr::_('Pictogram image'),
		);
	}
	
	public function afterAdd() : void
	{
		parent::afterAdd();
		Admin_Managers::FulltextSearch()->addIndex( static::get( $this->id ) );
	}
	
	public function afterUpdate() : void
	{
		parent::afterUpdate();
		Admin_Managers::FulltextSearch()->updateIndex( $this );
	}
	
	public function afterDelete() : void
	{
		parent::afterDelete();
		Admin_Managers::FulltextSearch()->deleteIndex( $this );
	}

	public function getAdminTitle(): string
	{
		$code = $this->internal_code ? : $this->id;
		
		return $this->getPathName().' ('.$code.')';
	}
}