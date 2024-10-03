<?php
namespace JetApplicationModule\Admin\Marketing\GiftProduct;

use Jet\DataModel_Definition;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_Gift_Product;

#[DataModel_Definition]
class Gift extends Marketing_Gift_Product implements Admin_Entity_Marketing_Interface
{
	use Admin_Entity_Marketing_Trait;
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function hasImages(): bool
	{
		return false;
	}
	
}