<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicaTionModule\EShop\ReturnsOfGoods;


use Jet\AJAX;
use Jet\Form;
use Jet\Form_Field_Email;
use Jet\Form_Field_Input;
use Jet\Form_Field_Int;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetApplication\Customer;
use JetApplication\Order;
use JetApplication\Product_EShopData;
use JetApplication\ReturnOfGoods;
use JetApplication\EShops;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		if( ($return = $this->getReturn()) ) {
			
			$this->view->setVar('return', $return);
			Navigation_Breadcrumb::addURL(
				Tr::_('Return of goods request %n% (Order:%on%)', [
					'n' => $return->getNumber(),
					'on' => Order::get( $return->getOrderId() )->getNumber()
				]),
				Http_Request::currentURI(unset_GET_params: ['product_id'])
			);

			
			if( $return->isEditable() ) {
				
				
				
				if( !$return->isCompleted() ) {
					if(
						Http_Request::GET()->exists('finish') &&
						$return->canBeFinished()
					) {
						$return->finish();
						$this->output('detail/just-finished');
					} else {
						$this->handleEditProblemDescription( $return );
						
						$this->output('detail/unfinished');
					}
				} else {
					$this->output('detail/edit');
				}
			} else {
				$this->output('detail/done');
			}
		} else {
			if(
				($order = $this->getOrder()) &&
				($product = $this->getProduct( $order ))
			) {
				$this->enterProblem( $order, $product );
			}
		}
	}
	
	protected function handleEditProblemDescription( ReturnOfGoods $return ) : void
	{
		$form = $return->getProblemDescriptionEditForm();
		$this->view->setVar('form', $form);
		
		if( $form->catchInput() ) {
			
			if($form->catch()) {
				$return->save();
			}
			
			AJAX::operationResponse(
				true,
				[
					'finish-btn' => $this->view->render('detail/unfinished/finish-btn')
				]
			);
			
		}
		
	}
	
	protected function getReturn() : ?ReturnOfGoods
	{
		$return = null;
		
		$GET = Http_Request::GET();
		
		if( ($return = ReturnOfGoods::getByURL()) ) {
			if( ($customer = Customer::getCurrentCustomer()) ) {
				if($return->getCustomerId()!=$customer->getId()) {
					$return = null;
				}
			}
		}
		
		return $return;
	}
	
	protected function getOrder() : ?Order
	{
		$order = null;
		$customer = Customer::getCurrentCustomer();
		
		$GET = Http_Request::GET();
		
		if(
			($order_key = $GET->getString('order')) &&
			($order = Order::getByKey( $order_key ))
		) {
			if( $customer ) {
				if($order->getCustomerId()!=$customer->getId()) {
					$order = null;
				}
			} else {
				if($GET->getString('m')!=sha1($order->getEmail())) {
					$order = null;
				}
			}
		}
		
		if($order) {
			return $order;
		}
		
		$order = null;
		
		$order_number_form = new Form('return_of_goods_order_number_form', []);
		
		
		$order_number_field = new Form_Field_Input('order_number', 'Order number:');
		$order_number_field->setErrorMessages([
			Form_Field_Int::ERROR_CODE_EMPTY => 'Please enter order number',
			'unknown_order' => 'Unknown order. Please check order number.'
		]);
		$order_number_field->setValidator( function() use ($order_number_field, $customer, &$order) {
			$on = $order_number_field->getValue();
			if(!$on) {
				$order_number_field->setError(Form_Field_Int::ERROR_CODE_EMPTY);
				return false;
			}
			
			$order = Order::getByNumber( $on, EShops::getCurrent() );
			
			if(!$order) {
				$order_number_field->setError( 'unknown_order' );
				return false;
			}
			
			if(
				$customer &&
				$order->getCustomerId()!=$customer->getId()
			) {
				$order_number_field->setError( 'unknown_order' );
				return false;
			}
			
			return true;
			
		} );
		$order_number_form->addField( $order_number_field );
		
		if(!Customer::getCurrentCustomer()) {
			$email_field = new Form_Field_Email('email', 'E-mail specified on the order:');
			$email_field->setErrorMessages([
				Form_Field_Email::ERROR_CODE_EMPTY => 'Please enter e-mail',
				Form_Field_Email::ERROR_CODE_INVALID_FORMAT => 'Please enter e-mail',
				'incorrect_email' => 'Sorry, but this is not the email listed on the order.',
			]);
			$email_field->setValidator( function() use ($email_field, &$order) {
				$email = $email_field->getValue();
				if(
					$order &&
					$order->getEmail()!=$email
				) {
					$email_field->setError('incorrect_email');
					return false;
				}
				
				return true;
			} );
			$order_number_form->addField( $email_field );
		}
		
		
		if($order_number_form->catch()) {
			$params = ['order'=>$order->getKey()];
			if($order_number_form->fieldExists('email')) {
				$params['m'] = sha1( $order_number_form->field('email')->getValue() );
			}
			
			Http_Headers::reload(set_GET_params: $params );
		}
		
		$this->view->setVar('order_number_form', $order_number_form);
		$this->output('default');
		
		return null;
	}
	
	protected function getProduct( Order $order ) : ?Product_EShopData
	{
		$this->view->setVar('order', $order);
		
		$GET = Http_Request::GET();
		$product_id = $GET->getInt('product_id');
		$product = null;
		foreach($order->getPhysicalProductOverview() as $item) {
			if($item->getProductId()==$product_id) {
				return $item->getProduct();
			}
		}
		
		Navigation_Breadcrumb::addURL(
			Tr::_('New return of goods request (Order:%n%) - select product', ['n'=>$order->getNumber()]),
			Http_Request::currentURI(unset_GET_params: ['product_id'])
		);
		
		$this->output('new/select-product');

		return null;
	}
	
	protected function enterProblem( Order $order, Product_EShopData $product ) : void
	{
		$this->view->setVar('product', $product);
		
		Navigation_Breadcrumb::addURL(
			Tr::_('New return of goods request (Order:%n%)', ['n'=>$order->getNumber()]),
			Http_Request::currentURI(unset_GET_params: ['product_id'])
		);
		
		Navigation_Breadcrumb::addURL(
			Tr::_('%p% - Please describe the problem', ['p'=>$product->getName()]),
		);
		
		$enter_problem_form = (new ReturnOfGoods())->getProblemDescriptionEditForm();
		
		if($enter_problem_form->catch()) {
			$return = ReturnOfGoods::startNew(
				$order,
				$product,
				$enter_problem_form->field('problem_description')->getValue()
			);
			
			Http_Headers::movedTemporary( $return->getURL() );
		}
		
		$this->view->setVar('enter_problem_form', $enter_problem_form);
		$this->output('new/enter-problem');
		
	}
	
}