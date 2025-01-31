<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Invoices;

class Handler_Invoicing_Main extends Handler
{
	public const KEY = 'invoicing';
	
	protected bool $has_dialog = false;
	
	
	protected function init() : void
	{
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return false;
	}
	
	
	public function handle(): void
	{
		if(
			!main::getCurrentUserCanEdit() ||
			$this->order->isCancelled()
		) {
			return;
		}
		
		$invoicing_action = Http_Request::GET()->getString('invoicing_action');

		if(!$invoicing_action) {
			return;
		}
		
		
		switch($invoicing_action) {
			case 'create_invoice':
				$invoice = Invoices::createInvoiceForOrder( $this->order );
				$invoice->save();
				UI_messages::success( Tr::_( 'Invoice %NUMBER% has been create', ['NUMBER' => $invoice->getNumber()] ) );
				break;
			case 'create_invoice_in_advance':
				$invoice = Invoices::createInvoiceInAdvanceForOrder( $this->order );
				$invoice->save();
				UI_messages::success( Tr::_( 'Invoice in advance %NUMBER% has been create', ['NUMBER' => $invoice->getNumber()] ) );
				break;
			case 'create_delivery_note':
				$invoice = Invoices::createDeliveryNoteForOrder( $this->order );
				$invoice->save();
				UI_messages::success( Tr::_( 'Delivery note %NUMBER% has been create', ['NUMBER' => $invoice->getNumber()] ) );
				break;
		}
		
		
		Http_Headers::reload(unset_GET_params: ['invoicing_action']);
	}
	
	
	public function canBeHandled() : bool
	{
		if(
			!Main::getCurrentUserCanEdit() ||
			$this->order->isCancelled()
		) {
			return false;
		}
		
		return true;
	}
}