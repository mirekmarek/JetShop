<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Orders;



use Jet\Form;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Customer_Address;

class Handler_ChangeBillingAddress_Main extends Handler {
	public const KEY = 'change_bolling_address';
	
	protected bool $has_dialog = true;
	
	protected Customer_Address $address;
	protected Form $form;
	
	protected function init() :void
	{
		$this->address = $this->order->getBillingAddress();
		
		$this->form = $this->address->createForm('billing_address_form');
		
		$this->view->setVar('billing_address_form', $this->form);
	}
	
	public function getForm(): Form
	{
		return $this->form;
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			
			$change = $this->order->updateBillingAddress( $this->address );
			
			if($change->hasChange()) {
				UI_messages::success(Tr::_('Billing address has been changed'));
			}
			
			Http_Headers::reload();
		}
	}
}