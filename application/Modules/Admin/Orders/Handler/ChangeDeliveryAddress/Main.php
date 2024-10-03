<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Form;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Customer_Address;

class Handler_ChangeDeliveryAddress_Main extends Handler
{
	public const KEY = 'change_delivery_address';
	
	protected bool $has_dialog = true;
	
	protected Customer_Address $address;
	protected Form $form;
	
	protected function init() : void
	{
		$this->address = $this->order->getDeliveryAddress();
		
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
			
			$change = $this->order->updateDeliveryAddress( $this->address );
			
			if($change->hasChange()) {
				UI_messages::success(Tr::_('Delivery address has been changed'));
			}
			
			Http_Headers::reload();
		}
	}
}