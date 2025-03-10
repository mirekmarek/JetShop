<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;



use Jet\Form;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Customer_Address;
use JetApplication\ReturnOfGoods;

class Plugin_ChangeDeliveryAddress_Main extends Plugin
{
	public const KEY = 'change_delivery_address';
	
	protected Customer_Address $address;
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		$this->address = $this->item->getDeliveryAddress();
		
		$this->form = $this->address->createForm('delivery_address_form');
		
		$this->form->removeField('company_id');
		$this->form->removeField('company_vat_id');
		
		$this->view->setVar('delivery_address_form', $this->form);
		
	}
	
	public function getForm(): Form
	{
		return $this->form;
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			/**
			 * @var ReturnOfGoods $item
			 */
			$item = $this->item;
			
			$change = $item->updateDeliveryAddress( $this->address );
			
			if($change->hasChange()) {
				UI_messages::success(Tr::_('Delivery address has been changed'));
			}
			
			Http_Headers::reload();
		}
	}
}