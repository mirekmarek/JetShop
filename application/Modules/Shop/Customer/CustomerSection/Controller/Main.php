<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Shop\Customer\CustomerSection;

use Jet\Form;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\EMailMarketing;
use JetApplication\Shop_Managers;
use JetApplication\Shop_Pages;
use JetApplication\Shops;
use JetApplication\Customer;
use JetApplication\Order;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	protected ?UI_tabs $tabs = null;
	protected string $selected_tab = '';
	
	protected function initTabs() : void
	{
		if(!$this->tabs) {
			$this->tabs = Main::initTabs( $this->selected_tab );
			
			$this->view->setVar('tabs', $this->tabs);
		}
		
	}

	public function resolve(): bool|string
	{
		$this->initTabs();
		
		return match ( $this->selected_tab ) {
			'orders' => 'orders',
			'reviews' => 'reviews',
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
				Shops::getCurrent(),
				$customer->getEmail(),
				'customer section'
			);
			Http_Headers::reload(unset_GET_params: ['unsubscribe']);
		}
		
		if($GET->exists('subscribe')) {
			EMailMarketing::SubscriptionManager()->subscribe(
				Shops::getCurrent(),
				$customer->getEmail(),
				'customer section'
			);
			Http_Headers::reload(unset_GET_params: ['subscribe']);
		}
		

		$this->output('newsletter_subscription');
	}
	
	public function reviews_Action() : void
	{
		$reviews_manager = Shop_Managers::ProductReviews();
		$reviews_manager->handleCustomerSectionReviews();
		
		$this->output('reviews');
	}

}