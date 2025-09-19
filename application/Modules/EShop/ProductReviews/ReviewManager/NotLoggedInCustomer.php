<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductReviews;


use Jet\Form;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Http_Headers;
use Jet\Http_Request;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\ProductReview;

class ReviewManager_NotLoggedInCustomer extends ReviewManager_Common
{
	protected ?Form $order_number_form = null;
	
	protected function init() : void
	{
		$order = null;
		
		$GET = Http_Request::GET();
		
		if(
			($order_key = $GET->getString('order')) &&
			($order = Order::getByKey( $order_key ))
		) {
			if($GET->getString('m')!=sha1($order->getEmail())) {
				$order = null;
			}
		}
		
		if(!$order) {
			return;
		}
		
		$this->order = $order;
		
		$this->already_written_reviews = [];
		
		$already_written = ProductReview::fetch([''=>['order_id'=>$this->order->getId()]], order_by: '-id');
		foreach($already_written as $review) {
			$this->already_written_reviews[$review->getProductId()] = $review;
		}
		
		
		$this->initPossibleProducts( [$this->order->getId()] );
		$this->initWriteReview();
	}
	
	public function getOrderNumberForm() : Form
	{
		if(!$this->order_number_form) {
			$this->order_number_form = new Form('order_number_form', []);
			
			
			$order_number_field = new Form_Field_Input('order_number', 'Order number:');
			$order_number_field->setErrorMessages([
				Form_Field_Int::ERROR_CODE_EMPTY => 'Please enter order number',
				'unknown_order' => 'Unknown order. Please check order number.'
			]);
			$order_number_field->setValidator( function() use ( $order_number_field ) {
				$on = $order_number_field->getValue();
				if(!$on) {
					$order_number_field->setError(Form_Field_Int::ERROR_CODE_EMPTY);
					return false;
				}
				
				$this->order = Order::getByNumber( $on, EShops::getCurrent() );
				
				if(!$this->order) {
					$order_number_field->setError( 'unknown_order' );
					return false;
				}
				
				return true;
				
			} );
			$this->order_number_form->addField( $order_number_field );
			
			$email_field = new Form_Field_Email('email', 'E-mail specified on the order:');
			$email_field->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter e-mail',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail',
				'incorrect_email' => 'Sorry, but this is not the email listed on the order.',
			]);
			$email_field->setValidator( function() use ( $email_field ) {
				$email = $email_field->getValue();
				if(
					$this->order &&
					$this->order->getEmail()!=$email
				) {
					$email_field->setError('incorrect_email');
					return false;
				}
				
				return true;
			} );
			$this->order_number_form->addField( $email_field );
		}
		
		return $this->order_number_form;
	}
	
	public function catchOrderNumberForm(): bool
	{
		if($this->getOrderNumberForm()->catch()) {
			$params = ['order'=>$this->order->getKey()];
			$params['m'] = sha1( $this->order->getEmail() );
			
			Http_Headers::reload(set_GET_params: $params );
			return true;
		}
		
		return false;
	}
}