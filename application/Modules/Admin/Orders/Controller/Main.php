<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Layout;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\EMail_Sent;
use JetApplication\Order;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_TotalAmount() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		$this->listing_manager->addColumn( new Listing_Column_DatePurchased() );
		
		$this->listing_manager->addFilter( new Listing_Filter_Source() );
		$this->listing_manager->addFilter( new Listing_Filter_Cancelled() );
		$this->listing_manager->addFilter( new Listing_Filter_PaymentRequired() );
		$this->listing_manager->addFilter( new Listing_Filter_Paid() );
		$this->listing_manager->addFilter( new Listing_Filter_AllItemsAvailable() );
		$this->listing_manager->addFilter( new Listing_Filter_Delivery() );
		$this->listing_manager->addFilter( new Listing_Filter_Payment() );
		$this->listing_manager->addFilter( new Listing_Filter_DatePurchased() );
		$this->listing_manager->addFilter( new Listing_Filter_Customer() );
		$this->listing_manager->addFilter( new Listing_Filter_Product() );
		
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			
			$search_separated = explode( ' ', $search );
			
			$q = [];
			
			if(count( $search_separated )==2) {
				$search_query_alt = addslashes($search_separated[1].' '.$search_separated[0]);
				$q[] = [
					'billing_first_name *' => '%'.$search_separated[0].'%',
					'AND',
					'billing_surname *' => '%'.$search_separated[1].'%'
				];
				$q[] = 'OR';
				$q[] = [
					'billing_first_name *' => '%'.$search_separated[1].'%',
					'AND',
					'billing_surname *' => '%'.$search_separated[0].'%'
				];
				$q[] = 'OR';
				
				$q[] = [
					'delivery_first_name *' => '%'.$search_separated[0].'%',
					'AND',
					'delivery_surname *' => '%'.$search_separated[1].'%'
				];
				$q[] = 'OR';
				$q[] = [
					'delivery_first_name *' => '%'.$search_separated[1].'%',
					'AND',
					'delivery_surname *' => '%'.$search_separated[0].'%'
				];
				
			}
			
			$q[] = 'OR';
			$q['phone *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['email *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['billing_company_name *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['delivery_company_name *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['billing_company_id *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['import_remote_id'] = $search;
			$q[] = 'OR';
			$q['number'] = $search;

			return $q;
		} );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			Listing_Column_Number::KEY,
			Listing_Column_Customer::KEY,
			Listing_Column_TotalAmount::KEY,
			Listing_Column_Items::KEY,
			Listing_Column_DatePurchased::KEY,
			'status_code'
		]);
		
		$this->listing_manager->setCustomBtnRenderer( function() : string {
			return $this->view->render('list/toolbar');
		} );
		
	}
	
	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		
		ListingHandler::initHandlers( $this->view, $this->getListing() );
		
		ListingHandler::handleHandlers();
		
		$output = $this->getListing()->renderListing();
		
		foreach(ListingHandler::getHandlers() as $handler) {
			$output .= $handler->renderDialog();
		}
		
		$this->content->output( $output );
	}


	public function add_Action() : void
	{
	}
	
	public function edit_main_Action() : void
	{
		/**
		 * @var Order $order
		 */
		$order = $this->current_item;
		
		if(($sent_email_id=Http_Request::GET()->getInt('show_sent_email'))) {
			$sent_email = EMail_Sent::load( $sent_email_id );
			if(!$sent_email) {
				Http_Headers::reload(unset_GET_params: ['show_sent_email']);
			}
			$this->view->setVar('sent_email', $sent_email);
			
			MVC_Layout::getCurrentLayout()->setScriptName('dialog');
			
			$this->output( 'sent_email' );
			return;
		}
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Order <b>%NUMBER%</b>', [ 'NUMBER' => $order->getNumber() ] )
		);
		
		$this->view->setVar( 'order', $order );
		$this->view->setVar('listing', $this->getListing());
		
		Handler::initHandlers( $this->view, $order );

		Handler::handleHandlers();
		
		if(Http_Request::GET()->exists('print')) {
			MVC_Layout::getCurrentLayout()->setScriptName('plain');
			$this->output( 'print' );
		} else {
			$this->output( 'edit' );
		}
	}
	
	
}