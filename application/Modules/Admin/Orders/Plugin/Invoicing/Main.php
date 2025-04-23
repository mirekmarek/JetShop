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
use JetApplication\Order;

class Plugin_Invoicing_Main extends Plugin
{
	public const KEY = 'invoicing';
	

	public function hasDialog(): bool
	{
		return false;
	}
	
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
			$this->item->isCancelled()
		) {
			return;
		}
		
		$invoicing_action = Http_Request::GET()->getString('invoicing_action');

		if(!$invoicing_action) {
			return;
		}
		
		/**
		 * @var Order $order
		 */
		$order = $this->item;
		
		switch($invoicing_action) {
			case 'create_invoice':
				$invoice = Invoices::createInvoiceForOrder( $order );
				$invoice->save();
				UI_messages::success( Tr::_( 'Invoice %NUMBER% has been create', ['NUMBER' => $invoice->getNumber()] ) );
				break;
			case 'create_proforma_invoice':
				$invoice = Invoices::createProformaInvoiceForOrder( $order );
				$invoice->save();
				UI_messages::success( Tr::_( 'Proforma Invoice %NUMBER% has been create', ['NUMBER' => $invoice->getNumber()] ) );
				break;
			case 'create_delivery_note':
				$invoice = Invoices::createDeliveryNoteForOrder( $order );
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
			$this->item->isCancelled()
		) {
			return false;
		}
		
		return true;
	}
}