<?php
namespace JetApplicationModule\Admin\Content\InfoBoxes;

use Jet\DataModel_Definition;
use Jet\Form;

use JetApplication\Content_InfoBox;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\Admin_Entity_WithEShopData_Trait;

#[DataModel_Definition]
class InfoBox extends Content_InfoBox implements Admin_Entity_WithEShopData_Interface
{
	
	use Admin_Entity_WithEShopData_Trait;
	
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
	
	
}


