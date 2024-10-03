<?php
/**
 *
 * @copyright
 * @license
 * @author
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

class Handler_ChangeEMailAndPhone_Main extends Handler {
	public const KEY = 'change_enail_and_phone';
	
	protected bool $has_dialog = true;
	
	protected Form $form;
	
	protected function init() : void
	{
		
		$this->form = new Form('phone_and_email_form', []);
		
		
		$email = new Form_Field_Email('email', 'E-Mail:');
		$email->setDefaultValue( $this->return_of_goods->getEmail() );
		$email->setIsRequired(true);
		$email->setErrorMessages([
			Form_Field_Email::ERROR_CODE_EMPTY => 'Invalid value',
			Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Invalid value',
			'used' => 'This e-mail is already used'
		]);
		if($this->return_of_goods->getCustomerId()) {
			$email->setValidator(function() use ($email) {
				$new_email_address = $email->getValue();
				if($new_email_address==$this->return_of_goods->getEmail()) {
					return true;
				}
				if(!$this->form->field('update_customer_account')->getValue()) {
					return true;
				}
				
				$exists = Customer::getByEmail( $new_email_address, $this->return_of_goods->getShop() );
				if(!$exists) {
					return true;
				}
				
				$email->setError('used');
				return false;
			});
		}
		$this->form->addField( $email );
		
		
		$phone = new Form_Field_Input('phone', 'Phone:');
		$phone->setDefaultValue( $this->return_of_goods->getPhone() );
		$phone->setIsRequired( true );
		$phone->setErrorMessages([
			Form_Field_Input::ERROR_CODE_EMPTY => 'Invalid value'
		]);
		$this->form->addField( $phone );
		
		if($this->return_of_goods->getCustomerId()) {
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
			CustomerBlacklist::customerIsBlacklisted( $this->return_of_goods->getEmail())
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
				$new_pone = $this->form->field('phone')->getValue();
				if($this->form->fieldExists('update_customer_account')) {
					$update_customer_account = $this->form->field('update_customer_account')->getValue();
				} else {
					$update_customer_account = false;
				}
				
				$change = $this->return_of_goods->startChange();
				$email_updated = false;
				
				if($new_email!=$this->return_of_goods->getEmail()) {
					
					$change->addChange('email', $this->return_of_goods->getEmail(), $new_email);
					$this->return_of_goods->setEmail( $new_email );
					$email_updated = true;
					
					UI_messages::success(Tr::_('E-mail has been changed'));
				}
				
				if($new_pone!=$this->return_of_goods->getPhone()) {
					
					$change->addChange('phone', $this->return_of_goods->getPhone(), $new_pone);
					$this->return_of_goods->setPhone( $new_pone );
					
					UI_messages::success(Tr::_('Phone has been changed'));
				}
				
				if($change->hasChange()) {
					$this->return_of_goods->save();
					$change->save();
				}
				
				if($update_customer_account) {
					$customer = Customer::get( $this->return_of_goods->getCustomerId() );
					
					if($customer->getEmail()!=$new_email) {
						
						$customer->changeEmail( $new_email, 'return_of_goods:'.$this->return_of_goods->getNumber() );
					}
					
					if($customer->getPhoneNumber()!=$new_pone) {
						
						$customer->setPhoneNumber( $new_pone );
						$customer->save();
					}
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