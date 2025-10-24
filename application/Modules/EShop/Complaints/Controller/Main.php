<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\Complaints;


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
use JetApplication\EShops;
use JetApplication\Complaint;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		if( ($complaint = $this->getComplaint()) ) {
			$complaint->handleShowImage();
			
			$this->view->setVar('complaint', $complaint);
			Navigation_Breadcrumb::addURL(
				Tr::_('Complaint %n% (Order:%on%)', [
					'n' => $complaint->getNumber(),
					'on' => Order::get( $complaint->getOrderId() )?->getNumber()??''
				]),
				Http_Request::currentURI(unset_GET_params: ['product_id'])
			);

			
			if( $complaint->isEditableByCustomer() ) {
				
				if( !$complaint->isCompleted()) {
					if(
						Http_Request::GET()->exists('finish') &&
						$complaint->canBeFinished()
					) {
						$complaint->finish();
						$this->output('detail/just-finished');
					} else {
						$this->handleImages( $complaint );
						$this->handleEditProblemDescription( $complaint );
						
						$this->output('detail/unfinished');
					}
				} else {
					$this->handleImages( $complaint );
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
	
	protected function handleEditProblemDescription( Complaint $complaint ) : void
	{
		$form = $complaint->getProblemDescriptionEditForm();
		$this->view->setVar('form', $form);
		
		if( $form->catchInput() ) {
			
			if($form->catch()) {
				$complaint->save();
			}
			
			AJAX::operationResponse(
				true,
				[
					'finish-btn' => $this->view->render('detail/unfinished/finish-btn')
				]
			);
			
		}
		
	}
	
	protected function handleImages( Complaint $complaint ) : void
	{
		if($complaint->getUploadImagesForm()->catchInput()) {
			$complaint->handleImageUpload();
			
			AJAX::operationResponse(
				true,
				[
					'complaint-images' => $this->view->render('detail/edit/images/list'),
					'finish-btn' => $this->view->render('detail/unfinished/finish-btn')
				]
			);
		}
		
		if(($delete_image_id=Http_Request::POST()->getInt('delete_image'))) {
			$complaint->deleteImage( $delete_image_id );
			
			AJAX::operationResponse(
				true,
				[
					'complaint-images' => $this->view->render('detail/edit/images/list'),
					'finish-btn' => $this->view->render('detail/unfinished/finish-btn')
				]
			);
			
		}
		
	}
	
	protected function getComplaint() : ?Complaint
	{
		$complaint = null;
		
		$GET = Http_Request::GET();
		
		if( ($complaint = Complaint::getByURL()) ) {
			if( ($customer = Customer::getCurrentCustomer()) ) {
				if($complaint->getCustomerId()!=$customer->getId()) {
					$complaint = null;
				}
			}
		}
		
		return $complaint;
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
		
		$order_number_form = new Form('complaint_order_number_form', []);
		
		
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
			Tr::_('New complaint (Order:%n%) - select product', ['n'=>$order->getNumber()]),
			Http_Request::currentURI(unset_GET_params: ['product_id'])
		);
		
		$this->output('new/select-product');

		return null;
	}
	
	protected function enterProblem( Order $order, Product_EShopData $product ) : void
	{
		$this->view->setVar('product', $product);
		
		Navigation_Breadcrumb::addURL(
			Tr::_('New complaint (Order:%n%)', ['n'=>$order->getNumber()]),
			Http_Request::currentURI(unset_GET_params: ['product_id'])
		);
		
		Navigation_Breadcrumb::addURL(
			Tr::_('%p% - Please describe the problem', ['p'=>$product->getName()]),
		);
		
		$enter_problem_form = (new Complaint())->getProblemDescriptionEditForm();
		
		if($enter_problem_form->catch()) {
			$complaint = Complaint::startNew(
				$order,
				$product,
				$enter_problem_form->field('problem_description')->getValue()
			);
			
			Http_Headers::movedTemporary( $complaint->getURL() );
		}
		
		$this->view->setVar('enter_problem_form', $enter_problem_form);
		$this->output('new/enter-problem');
		
	}
	
}