<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Customer\CustomerSection;


use Jet\Form;
use Jet\Form_Field;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Form_Field_Password;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI_messages;
use Jet\UI_tabs;
use JetApplication\EMailMarketing;
use JetApplication\EShops;
use JetApplication\Customer;
use JetApplication\Order;


class Controller_Main extends MVC_Controller_Default
{
	protected ?UI_tabs $tabs = null;
	protected string $selected_tab = '';
	
	protected function initTabs() : void
	{
		if(!$this->tabs) {
			$this->tabs = Main::initTabs(  $this->selected_tab );
			
			$this->view->setVar('tabs', $this->tabs);
		}
		
	}

	public function resolve(): bool|string
	{
		$this->initTabs();
		
		return match ( $this->selected_tab ) {
			'orders' => 'orders',
			'addresses' => 'addresses',
			'newsletter-subscription' => 'newsletter_subscription',

			default => 'basic',
		};
	}
	
	public function basic_Action(): void
	{
		$customer = Customer::getCurrentCustomer();
		
		if(Http_Request::GET()->getString('edit')=='basic-info') {
			$customer = Customer::getCurrentCustomer();
			
			$email = new Form_Field_Email('email', 'E-mail:');
			$email->setDefaultValue( $customer->getEmail() );
			$email->setIsRequired(true);
			$email->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter e-mail',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail',
				'exists' => 'E-mail is already used'
			]);
			$email->setValidator( function() use ($email, $customer) {
				if($email->getValue()==$customer->getEmail()) {
					return true;
				}
				
				if(Customer::getByEmail( $email->getValue() )) {
					$email->setError('exists');
					return false;
				}
				return true;
			} );
			$email->setFieldValueCatcher( function( string $value ) use ($customer) {
				$customer->changeEmail( $value, 'customer section' );
			} );
			
			
			$phone = new Form_Field_Input('phone', 'Phone number:');
			$phone->setDefaultValue( $customer->getPhoneNumber() );
			$phone->setIsRequired(true);
			$phone->setErrorMessages([
				Form_Field_Input::ERROR_CODE_EMPTY => 'Please enter phone number',
				Form_Field_Input::ERROR_CODE_INVALID_FORMAT => 'Please enter phone number',
			]);
			$phone->setFieldValueCatcher( function( string $value ) use ($customer) {
				$customer->setPhoneNumber( $value );
			} );
			
			
			$first_name = new Form_Field_Input('first_name', 'First name:');
			$first_name->setDefaultValue( $customer->getFirstName() );
			$first_name->setIsRequired(true);
			$first_name->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter first name',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter first name',
			]);
			$first_name->setFieldValueCatcher( function( string $value ) use ($customer) {
				$customer->setFirstName( $value );
			} );
			
			$surname = new Form_Field_Input('surname', 'First name:');
			$surname->setDefaultValue( $customer->getSurname() );
			$surname->setIsRequired(true);
			$surname->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter first name',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter first name',
			]);
			$surname->setFieldValueCatcher( function( string $value ) use ($customer) {
				$customer->setSurname( $value );
			} );
			
			
			$form = new Form('basic_info_form', [
				$email,
				$phone,
				$first_name,
				$surname
			]);

			if($form->catch()) {
				Http_Headers::reload(unset_GET_params: ['edit']);
			}
			
			$this->view->setVar('form', $form);
		}
		
		
		$customer = Customer::getCurrentCustomer();
		if(!$customer->getOauthService())
		{
			$current_password = new Form_Field_Password( 'current_password', 'Current password' );
			$current_password->setIsRequired( true );
			$current_password->setErrorMessages(
				[
					Form_Field::ERROR_CODE_EMPTY => 'Please enter new password',
					'incorect_password' => 'Incorrect password',
				
				]
			);
			$current_password->setValidator( function( Form_Field_Password $field ) use ($customer) : bool {
				if(!$customer->verifyPassword($field->getValue())) {
					$field->setError('incorect_password');
					return false;
				}
				
				return true;
			} );
			
			
			$password = new Form_Field_Password( 'password', 'New password: ' );
			$password->setIsRequired( true );
			$password->setErrorMessages(
				[
					Form_Field::ERROR_CODE_EMPTY         => 'Please enter new password',
				]
			);
			
			
			$password_check = $password->generateCheckField(
				field_name: 'password_check',
				field_label: 'Confirm new password',
				error_message_empty: 'Please confirm new password',
				error_message_not_match: 'Password confirmation do not match'
			);
			
			
			$change_password_form = new Form(
				'change_password', [
					$current_password,
					$password,
					$password_check
				]
			);
			
			if( $change_password_form->catchInput() && $change_password_form->validate() ) {
				$data = $change_password_form->getValues();
				
				$customer->setPassword( $data['password'] );
				$customer->setPasswordIsValid( true );
				$customer->setPasswordIsValidTill( null );
				$customer->save();
				
				UI_messages::success( Tr::_('Your password has been changed'), 'password_change' );
				
				Logger::info(
					event: 'password_changed',
					event_message: 'Customer ' . $customer->getUsername() . ' (id:' . $customer->getId() . ') changed password',
					context_object_id: $customer->getId(),
					context_object_name: $customer->getUsername()
				);
				
				Http_Headers::reload();
			}
			
			$this->view->setVar( 'change_password_form', $change_password_form );
			
			
		}
		
		
		
		$this->output('basic');
	}
	
	
	public function orders_Action(): void
	{
		$customer = Customer::getCurrentCustomer();
		
		$order = null;
		$number = Http_Request::GET()->getString('order');
		if($number) {
			$order = Order::load([
				'customer_id' => $customer->getId(),
				'AND',
				'number' => $number
			]);
		}
		
		if($order) {
			$this->view->setVar('order', $order);
			
			$this->output('order');
			
		} else {
			$orders = Order::fetchInstances([
				'customer_id' => $customer->getId()
			]);
			$orders->getQuery()->setOrderBy('-id');
			
			$this->view->setVar('orders', $orders);
			
			$this->output('orders');
		}
		
	}
	
	public function addresses_Action(): void
	{
		$customer = Customer::getCurrentCustomer();
		$GET = Http_Request::GET();
		$current_address = null;
		
		if(($set_default=$GET->getInt('set_default'))) {
			$customer->getAddress( $set_default )?->setIsDefault();
			Http_Headers::reload(unset_GET_params: ['set_default']);
		}
		
		if(($delete=$GET->getInt('delete'))) {
			$delete = $customer->getAddress( $delete );
			if($delete && !$delete->isDefault()) {
				$delete->delete();
			}
			
			Http_Headers::reload(unset_GET_params: ['delete']);
		}
		
		
		if(($edit=$GET->getInt('edit'))) {
			$current_address = $customer->getAddress($edit);
		}
		
		$address_edit_form = null;
		if($current_address) {
			$address_edit_form = $current_address->createForm('edit_form');
			if($address_edit_form->catch()) {
				$current_address->save();
				Http_Headers::reload();
			}
		}
		
		$this->view->setVar('current_address', $current_address);
		$this->view->setVar('address_edit_form', $address_edit_form);
		$this->output('addresses');
	}
	
	public function newsletter_subscription_Action(): void
	{
		$customer = Customer::getCurrentCustomer();
		
		$GET = Http_Request::GET();
		
		if($GET->exists('unsubscribe')) {
			EMailMarketing::SubscriptionManager()->unsubscribe(
				EShops::getCurrent(),
				$customer->getEmail(),
				'customer section'
			);
			Http_Headers::reload(unset_GET_params: ['unsubscribe']);
		}
		
		if($GET->exists('subscribe')) {
			EMailMarketing::SubscriptionManager()->subscribe(
				EShops::getCurrent(),
				$customer->getEmail(),
				'customer section'
			);
			Http_Headers::reload(unset_GET_params: ['subscribe']);
		}
		

		$this->output('newsletter_subscription');
	}
	
}