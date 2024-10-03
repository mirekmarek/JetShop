<?php
namespace JetApplicationModule\Admin\Content\Article\KindOf;

use Jet\DataModel_Definition;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Content_Article_KindOfArticle;

#[DataModel_Definition]
class KindOfArticle extends Content_Article_KindOfArticle implements Admin_Entity_Common_Interface
{
	use Admin_Entity_Common_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	
}