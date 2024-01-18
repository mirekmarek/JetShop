<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\OrderStatus;


use Jet\DataModel_Definition;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Order_Status;


#[DataModel_Definition]
class OrderStatus extends Order_Status implements Admin_Entity_WithShopData_Interface
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
			image_title:  Tr::_('Icon 2'),
		);
		
	}
	
	public function isItPossibleToDelete() : bool
	{
		return true;
	}
}