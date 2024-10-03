<?php
namespace JetApplicationModule\Admin\Content\Email\Templates;

use Jet\DataModel_Definition;
use Jet\Form;

use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\EMail_TemplateText;

#[DataModel_Definition]
class EmailTemplateText extends EMail_TemplateText implements Admin_Entity_WithShopData_Interface
{
	
	use Admin_Entity_WithShopData_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditUrl( $this->id );
	}
	
	protected function setupEditForm( Form $form ) : void
	{
		$form->field('internal_code')->setIsReadonly( true );
	}
	
	protected function setupAddForm( Form $form ): void
	{
	}
	
	public function defineImages() : void
	{
	}
	
	
}


