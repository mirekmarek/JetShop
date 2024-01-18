<?php
namespace JetApplicationModule\Admin\Suppliers;

use Jet\DataModel_Definition;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Supplier as Application_Supplier;

#[DataModel_Definition]
class Supplier extends Application_Supplier implements Admin_Entity_Common_Interface
{
	use Admin_Entity_Common_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	
}