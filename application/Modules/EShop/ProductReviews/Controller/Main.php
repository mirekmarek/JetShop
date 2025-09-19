<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\EShop\ProductReviews;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Controller_Default;
use Jet\Navigation_Breadcrumb;
use JetApplication\Customer;


class Controller_Main extends MVC_Controller_Default
{

	/**
	 *
	 */
	public function default_Action() : void
	{
		
		if( Customer::getCurrentCustomer() ) {
			$this->default_customerLoggedIn_Action();
		} else {
			$this->default_customerNotLoggedIn_Action();
		}
	}
	
	public function default_customerLoggedIn_Action() : void
	{
		$cs_manager = new ReviewManager_LoggedInCustomer();
		$this->view->setVar('manager', $cs_manager);
		
		if($cs_manager->getProduct()) {
			if($cs_manager->catchWriteReviewForm()) {
				Http_Headers::reload();
			}
			
			Navigation_Breadcrumb::addURL(
				$cs_manager->getProduct()->getName()
			);
			
			$this->output('write_review');
		} else {
			$this->output('select_product');
		}
	}
	
	
	
	public function default_customerNotLoggedIn_Action() : void
	{
		$cs_manager = new ReviewManager_NotLoggedInCustomer();
		$this->view->setVar('manager', $cs_manager);
		
		if(!$cs_manager->getOrder()) {
			$cs_manager->catchOrderNumberForm();
			
			$this->output('not-logged/select_order');
		} else {
			if($cs_manager->getProduct()) {
				if($cs_manager->catchWriteReviewForm()) {
					Http_Headers::reload();
				}
				
				Navigation_Breadcrumb::getCurrentLastItem()->setURL(
					Http_Request::currentURI(unset_GET_params: ['write_review'])
				);
				
				Navigation_Breadcrumb::addURL(
					$cs_manager->getProduct()->getName()
				);
				
				$this->output('write_review');
			} else {
				$this->output('select_product');
			}
		}
	}
	
}