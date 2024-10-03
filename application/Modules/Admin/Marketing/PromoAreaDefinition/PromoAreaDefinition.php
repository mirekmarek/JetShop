<?php
namespace JetApplicationModule\Admin\Marketing\PromoAreaDefinition;

use Jet\DataModel_Definition;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Marketing_PromoAreaDefinition;

#[DataModel_Definition]
class PromoAreaDefinition extends Marketing_PromoAreaDefinition implements Admin_Entity_Common_Interface
{
	use Admin_Entity_Common_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	
}