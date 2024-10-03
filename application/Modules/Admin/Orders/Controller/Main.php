<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Layout;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Entity_Listing;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use JetApplication\EMail_Sent;


class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	protected ?Order $order = null;
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;


	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->order = Order::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => Main::ACTION_GET,
					'edit'   => Main::ACTION_UPDATE,
				]
			);
		}

		return $this->router;
	}
	
	protected function setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}
	
	public function getListing() : Admin_Managers_Entity_Listing
	{
		if(!$this->listing_manager) {
			$this->listing_manager = Admin_Managers::EntityListing();
			$this->listing_manager->setUp(
				$this->module
			);
			
			$this->setupListing();
		}
		
		return $this->listing_manager;
	}
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_TotalAmount() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		$this->listing_manager->addColumn( new Listing_Column_DatePurchased() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
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
			'shop',
			Listing_Column_Number::KEY,
			Listing_Column_Customer::KEY,
			Listing_Column_TotalAmount::KEY,
			Listing_Column_Items::KEY,
			Listing_Column_DatePurchased::KEY,
			Listing_Column_Status::KEY
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
	
	public function edit_Action() : void
	{
		/**
		 * @var Order $order
		 */
		$order = $this->order;
		
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
		
		Handler::initHandlers( $this->view, $this->order );

		Handler::handleHandlers();
		
		if(Http_Request::GET()->exists('print')) {
			MVC_Layout::getCurrentLayout()->setScriptName('plain');
			$this->output( 'print' );
		} else {
			$this->output( 'edit' );
		}
	}
	
	
}