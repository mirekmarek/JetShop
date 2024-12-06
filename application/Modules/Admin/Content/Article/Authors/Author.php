<?php
namespace JetApplicationModule\Admin\Content\Article\Authors;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Content_Article_Author;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;

#[DataModel_Definition]
class Author extends Content_Article_Author implements Admin_Entity_WithEShopData_Interface
{
	
	use Admin_Entity_WithEShopData_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'avatar_1',
			image_title:  Tr::_('Avatar 1'),
		);
		$this->defineImage(
			image_class:  'avatar_2',
			image_title:  Tr::_('Avatar 2'),
		);
	}
	
	
}