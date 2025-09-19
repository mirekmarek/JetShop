<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;



use Jet\Form;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\EShopEntity_Address;
use JetApplication\Order;

class Plugin_ChangeBillingAddress_Main extends Plugin {
	public const KEY = 'change_bolling_address';
	
	protected EShopEntity_Address $address;
	protected Form $form;
	
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() :void
	{
		$this->address = $this->item->getBillingAddress();
		
		$this->form = $this->address->createForm('billing_address_form');
		
		$this->view->setVar('billing_address_form', $this->form);
	}
	
	public function getForm(): Form
	{
		return $this->form;
	}
	
	public function handle() : void
	{
		/**
		 * @var Order $item
		 */
		$item = $this->item;
		
		if($this->form->catch()) {
			
			$change = $item->updateBillingAddress( $this->address );
			
			if($change->hasChange()) {
				UI_messages::success(Tr::_('Billing address has been changed'));
			}
			
			Http_Headers::reload();
		}
	}
}