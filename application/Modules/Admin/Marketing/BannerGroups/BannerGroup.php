<?php
namespace JetApplicationModule\Admin\Marketing\BannerGroups;

use Jet\DataModel_Definition;
use JetApplication\Admin_Entity_Common_Interface;
use JetApplication\Admin_Entity_Common_Trait;
use JetApplication\Marketing_BannerGroup;

#[DataModel_Definition]
class BannerGroup extends Marketing_BannerGroup implements Admin_Entity_Common_Interface
{
	use Admin_Entity_Common_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	
}