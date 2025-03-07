<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\MoneyRefunds;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Layout;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\EMail_Sent;
use JetApplication\MoneyRefund;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_DateStarted() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		$this->listing_manager->addColumn( new Listing_Column_Amount() );
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		
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
			'status_code',
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
		 * @var MoneyRefund $refund
		 */
		$refund = $this->current_item;
		
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
			Tr::_( 'Money refundation <b>%NUMBER%</b>', [ 'NUMBER' => $refund->getNumber() ] )
		);
		
		$this->view->setVar( 'money_refund', $refund );
		$this->view->setVar('listing', $this->getListing());
		
		Handler::initHandlers( $this->view, $refund );
		Handler::handleHandlers();
		
		$this->output( 'edit' );

	}
	
	
}