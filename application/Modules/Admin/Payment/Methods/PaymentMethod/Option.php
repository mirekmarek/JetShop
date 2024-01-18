<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Payment_Method_Option;
use JetApplicationModule\Admin\Catalog\Stickers\Main;

class PaymentMethod_Option extends Payment_Method_Option implements Admin_Entity_WithShopData_Interface
{
	use Admin_Entity_WithShopData_Trait;
	
	
	public function getEditURL() : string
	{
		return Main::getEditURL( $this->id );
	}
	
	public function defineImages() : void
	{
		$this->defineImage(
			image_class:  'icon1',
			image_title:  Tr::_('Icon 1'),
		);
		$this->defineImage(
			image_class:  'icon2',
			image_title:  Tr::_('Icon 3'),
		);
		$this->defineImage(
			image_class:  'icon3',
			image_title:  Tr::_('Icon 3'),
		);
		
	}
	
	
	public function isItPossibleToDelete() : bool
	{
		return true;
	}
}