<?php
namespace JetApplicationModule\Admin\Marketing\GiftShoppingCart;

use Jet\DataModel_Definition;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\Admin_Entity_Marketing_Trait;
use JetApplication\Marketing_Gift_ShoppingCart;

#[DataModel_Definition]
class Gift extends Marketing_Gift_ShoppingCart implements Admin_Entity_Marketing_Interface
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