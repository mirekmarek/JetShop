<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Layout;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_EShopEntity_Listing;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use JetApplication\EMail_Sent;
use JetApplication\Complaint;


class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	protected ?Complaint $complaint = null;
	
	protected ?Admin_Managers_EShopEntity_Listing $listing_manager = null;


	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->complaint = Complaint::get((int)$id));
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
		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}
	
	public function getListing() : Admin_Managers_EShopEntity_Listing
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
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_DateStarted() );
		$this->listing_manager->addColumn( new Listing_Column_StatusId() );
		$this->listing_manager->addColumn( new Listing_Column_DateStarted() );
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_Product() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search_separated = explode( ' ', $search );
			
			$q = [];
			
			if(count( $search_separated )==2) {
				$search_query_alt = addslashes($search_separated[1].' '.$search_separated[0]);
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
			$q['delivery_company_name *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['number'] = $search;
			
			return $q;
			
		} );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			'number',
			'customer',
			'total_amount',
			'items',
			'date_purchased',
			'status_id'
		]);
		
	}
	
	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$this->content->output( $this->getListing()->renderListing() );
	}


	public function add_Action() : void
	{
	}
	
	public function edit_Action() : void
	{
		/**
		 * @var Complaint $complaint
		 */
		$complaint = $this->complaint;
		
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
			Tr::_( 'Complaint <b>%NUMBER%</b>', [ 'NUMBER' => $complaint->getNumber() ] )
		);
		
		$this->view->setVar( 'complaint', $complaint );
		$this->view->setVar('listing', $this->getListing());
		
		Handler::initHandlers( $this->view, $this->complaint );
		Handler::handleHandlers();
		
		$this->output( 'edit' );

	}
	
	
}