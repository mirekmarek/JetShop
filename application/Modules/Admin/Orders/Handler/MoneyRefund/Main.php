<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;



use Jet\Form;
use Jet\Form_Field_Float;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\MoneyRefund;

class Handler_MoneyRefund_Main extends Handler {
	public const KEY = 'money_refund';
	
	protected Form $form;
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
		$internal_summary = new Form_Field_Textarea('internal_summary', 'Internal Summary:');
		$amount_to_be_refunded = new Form_Field_Float('amount_to_be_refunded', 'Amount to be refunded:');
		$amount_to_be_refunded->setMaxValue( $this->order->getTotalAmount_WithVAT() );
		$amount_to_be_refunded->setIsRequired( true );
		$amount_to_be_refunded->setDefaultValue( $this->order->getTotalAmount_WithVAT() );
		
		$this->form = new Form('money_refund_form', [ $internal_summary, $amount_to_be_refunded ]);
		$this->view->setVar('money_refund_form', $this->form);
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			$refund = new MoneyRefund();
			$refund->setOrder( $this->order );
			$refund->setContext( $this->order->getProvidesContext() );
			
			$refund->setAmountToBeRefunded( $this->form->getField('amount_to_be_refunded')->getValue() );
			$refund->setInternalSummary( $this->form->getField('internal_summary')->getValue() );
			
			$refund->save();
			
			UI_messages::success(Tr::_('Money refundation request has been created'));
			Http_Headers::reload();
		}
	}
}