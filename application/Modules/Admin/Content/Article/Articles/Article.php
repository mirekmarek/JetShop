<?php
namespace JetApplicationModule\Admin\Content\Article\Articles;

use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Content_Article;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;

#[DataModel_Definition]
class Article extends Content_Article implements Admin_Entity_WithShopData_Interface
{
	
	use Admin_Entity_WithShopData_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'header_1',
			image_title:  Tr::_('Header 1'),
		);
		$this->defineImage(
			image_class:  'header_2',
			image_title:  Tr::_('Header 2'),
		);
	}
	
	
}