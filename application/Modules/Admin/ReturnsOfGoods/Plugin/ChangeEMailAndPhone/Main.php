<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;



use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Checkbox;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Customer;
use JetApplication\CustomerBlacklist;
use JetApplication\ReturnOfGoods;

class Plugin_ChangeEMailAndPhone_Main extends Plugin {
	public const KEY = 'change_enail_and_phone';
	
	protected Form $form;
	
	public function hasDialog(): bool
	{
		return true;
	}
	
	protected function init() : void
	{
		
		$this->form = new Form('phone_and_email_form', []);
		
		/**
		 * @var ReturnOfGoods $return
		 */
		$return = $this->item;
		
		$email = new Form_Field_Email('email', 'E-Mail:');
		$email->setDefaultValue( $return->getEmail() );
		$email->setIsRequired(true);
		$email->setErrorMessages([
			Form_Field_Email::ERROR_CODE_EMPTY => 'Invalid value',
			Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Invalid value',
			'used' => 'This e-mail is already used'
		]);
		if($return->getCustomerId()) {
			$email->setValidator(function() use ($email) {
				/**
				 * @var ReturnOfGoods $item
				 */
				$item = $this->item;
				
				$new_email_address = $email->getValue();
				if($new_email_address==$item->getEmail()) {
					return true;
				}
				if(!$this->form->field('update_customer_account')->getValue()) {
					return true;
				}
				
				$exists = Customer::getByEmail( $new_email_address, $item->getEshop() );
				if(!$exists) {
					return true;
				}
				
				$email->setError('used');
				return false;
			});
		}
		$this->form->addField( $email );
		
		
		$phone = new Form_Field_Input('phone', 'Phone:');
		$phone->setDefaultValue( $return->getPhone() );
		$phone->setIsRequired( true );
		$phone->setErrorMessages([
			Form_Field_Input::ERROR_CODE_EMPTY => 'Invalid value'
		]);
		$this->form->addField( $phone );
		
		
		$ba_number = new Form_Field_Input('bank_account_number', 'Bank account number:');
		$ba_number->setDefaultValue( $return->getBankAccountNumber() );
		$this->form->addField( $ba_number );
		
		if($return->getCustomerId()) {
			$update_customer_account = new Form_Field_Checkbox('update_customer_account', 'Update customer account');
			$update_customer_account->setDefaultValue(true);
			
			$this->form->addField( $update_customer_account );
		}
		
		
		
		$this->view->setVar('phone_and_email_form', $this->form);
		
	}
	
	public function canBeHandled(): bool
	{
		if(
			!parent::canBeHandled() ||
			CustomerBlacklist::customerIsBlacklisted( $this->item->getEmail())
		) {
			return false;
		}
		
		return true;
	}
	
	public function getForm(): Form
	{
		return $this->form;
	}
	
	public function handle() : void
	{
		if($this->form->catchInput()) {
			
			if($this->form->validate()) {
				$new_email = $this->form->field('email')->getValue();
				$new_phone = $this->form->field('phone')->getValue();
				$new_ba_number = $this->form->field('bank_account_number')->getValue();
				
				if($this->form->fieldExists('update_customer_account')) {
					$update_customer_account = $this->form->field('update_customer_account')->getValue();
				} else {
					$update_customer_account = false;
				}
				
				/**
				 * @var ReturnOfGoods $item
				 */
				$item = $this->item;
				
				$change = $item->updateEmailAndPhone(
					new_email: $new_email,
					new_phone: $new_phone,
					update_customer_account: $update_customer_account
				);
				
				if($change->hasChange('email')) {
					UI_messages::success(Tr::_('E-mail has been changed'));
				}
				if($change->hasChange('phone')) {
					UI_messages::success( Tr::_( 'Phone has been changed' ) );
				}
				
				$change = $change = $item->updateBankAccountNumber( $new_ba_number );
				if($change->hasChange('bank_account_number')) {
					UI_messages::success( Tr::_( 'Bank account number has been updated' ) );
				}
				
				
				AJAX::operationResponse(true);
			} else {
				AJAX::operationResponse(false, [
					'phone-and-email-update-area' => $this->view->render('form')
				]);
			}
		}
	}
	
	
	
}