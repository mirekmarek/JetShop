<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;


use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\MoneyRefund;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_DateStarted() );
		$this->listing_manager->addColumn( new Listing_Column_Amount() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search_separated = explode( ' ', $search );
			
			$q = [];
			
			if(count( $search_separated )==2) {
				$search_query_alt = addslashes($search_separated[1].' '.$search_separated[0]);
				$q[] = [
					'first_name *' => '%'.$search_separated[0].'%',
					'AND',
					'surname *' => '%'.$search_separated[1].'%'
				];
				$q[] = 'OR';
				$q[] = [
					'first_name *' => '%'.$search_separated[1].'%',
					'AND',
					'surname *' => '%'.$search_separated[0].'%'
				];
				
			}
			$q[] = 'OR';
			$q['number *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['order_number *'] = '%'.$search.'%';
			
			$q[] = 'OR';
			$q['phone *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['email *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['company_name *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['number'] = $search;
			
			return $q;
			
		} );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			'number',
			'customer',
			'status',
			'order_number',
			'date_started',
			'amount_to_be_refunded'
		]);
		
	}
	

	public function add_Action() : void
	{
	}
	
	public function edit_main_Action() : void
	{
		/**
		 * @var MoneyRefund $item
		 */
		$item = $this->current_item;
		
		if(($sent_email=Application_Service_Admin::EntityEdit()->handleShowSentEmail( $item ))) {
			$this->content->output( $sent_email );
			return;
		}
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Money refundation <b>%NUMBER%</b>', [ 'NUMBER' => $item->getNumber() ] )
		);
		
		$this->view->setVar( 'money_refund', $item );
		$this->view->setVar('listing', $this->getListing());
		
		Plugin::initPlugins( $this->view, $item );
		$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
		
		if(Main::getCurrentUserCanEdit()) {
			Plugin::handlePlugins();
		}
		
		$this->output( 'edit' );

	}
	
	
}