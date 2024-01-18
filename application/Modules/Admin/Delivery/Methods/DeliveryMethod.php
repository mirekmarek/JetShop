<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Delivery\Methods;

use Jet\DataModel_Definition;
use Jet\Form;
use Jet\Tr;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Trait;
use JetApplication\Delivery_Method;
use JetApplication\Shops;


#[DataModel_Definition]
class DeliveryMethod extends Delivery_Method implements Admin_Entity_WithShopData_Interface
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
		
		$this->defineImage(
			image_class:  'icon3',
			image_title:  Tr::_('Icon 3'),
		);
		
		
	}
	
	public function getAddForm(): Form
	{
		if(!$this->_add_form) {
			$this->_add_form = $this->createForm('add_form');
			
			foreach(Shops::getList() as $shop) {
				$this->_add_form->field( '/shop_data/' . $shop->getKey() . '/vat_rate' )->setDefaultValue( $shop->getDefaultVatRate() );
			}
		}
		
		return $this->_add_form;
	}
	
	public function isItPossibleToDelete() : bool
	{
		return true;
	}
}