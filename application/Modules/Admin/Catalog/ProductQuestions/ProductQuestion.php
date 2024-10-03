<?php
namespace JetApplicationModule\Admin\Catalog\ProductQuestions;

use Jet\DataModel_Definition;
use Jet\Form;

use JetApplication\Admin_Entity_WithShopRelation_Interface;
use JetApplication\Admin_Entity_WithShopRelation_Trait;
use JetApplication\ProductQuestion as Application_ProductQuestion;

#[DataModel_Definition]
class ProductQuestion extends Application_ProductQuestion implements Admin_Entity_WithShopRelation_Interface
{
	
	use Admin_Entity_WithShopRelation_Trait;
	
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


