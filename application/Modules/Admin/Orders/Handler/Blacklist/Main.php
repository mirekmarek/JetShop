<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\Admin\Orders;



use Jet\Form;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Form_Field_Textarea;
use Jet\Http_Headers;
use JetApplication\CustomerBlacklist;

class Handler_Blacklist_Main extends Handler {
	public const KEY = 'blacklist';
	
	protected Form $form;
	
	protected bool $has_dialog = true;
	
	protected function init() : void
	{
		$email = new Form_Field_Email('email', 'E-mail:');
		$email->setDefaultValue( $this->order->getEmail() );
		$email->setIsRequired(true);
		$email->setErrorMessages([
			Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Invalid value',
			Form_Field_Email::ERROR_CODE_EMPTY => 'Invalid value',
		]);
		
		$name = new Form_Field_Input('name', 'Name:');
		$name->setDefaultValue( $this->order->getBillingFirstName().' '.$this->order->getBillingSurname() );
		$name->setIsRequired(true);
		$name->setErrorMessages([
			Form_Field_Email::ERROR_CODE_EMPTY => 'Invalid value',
		]);
		
		
		$description = new Form_Field_Textarea('description', 'Notes:');
		
		$this->form = new Form('add_bl_form', [
			$email,
			$name,
			$description
		]);
		
		$this->view->setVar('add_bl_form', $this->form);
		
	}
	
	public function handleOnlyIfOrderIsEditable() : bool
	{
		return false;
	}
	
	public function getForm(): Form
	{
		return $this->form;
	}
	
	public function handle() : void
	{
		if($this->form->catch()) {
			CustomerBlacklist::add(
				eshop: $this->order->getEshop(),
				email: $this->form->field('email')->getValue(),
				name: $this->form->field('name')->getValue(),
				description: $this->form->field('description')->getValue()
			);
			Http_Headers::reload();
			
		}
	}
	
	
	
}