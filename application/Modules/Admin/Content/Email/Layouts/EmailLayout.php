<?php
namespace JetApplicationModule\Admin\Content\Email\Layouts;

use Jet\DataModel_Definition;
use Jet\Form;

use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\EMail_Layout;

#[DataModel_Definition]
class EmailLayout extends EMail_Layout implements Admin_Entity_WithShopData_Interface
{
	
	use Admin_Entity_WithShopData_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
	}
	
	protected function setupAddForm( Form $form ): void
	{
	}
	
	public function defineImages() : void
	{
	}
	
	
}


