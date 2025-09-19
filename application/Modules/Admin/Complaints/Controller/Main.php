<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\Complaint;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_DateStarted() );
		
		
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
			'status'
		]);
		
	}
	

	public function add_Action() : void
	{
	}
	
	public function edit_main_Action() : void
	{
		/**
		 * @var Complaint $complaint
		 */
		$complaint = $this->current_item;
		
		if(($sent_email=Application_Service_Admin::EntityEdit()->handleShowSentEmail( $complaint ))) {
			$this->content->output( $sent_email );
			return;
		}
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Complaint <b>%NUMBER%</b>', [ 'NUMBER' => $complaint->getNumber() ] )
		);
		
		$this->view->setVar( 'complaint', $complaint );
		$this->view->setVar('listing', $this->getListing());
		
		Plugin::initPlugins( $this->view, $complaint );
		$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
		
		if(Main::getCurrentUserCanEdit()) {
			Plugin::handlePlugins();
		}
		
		$this->output( 'edit' );

	}
	
	
}