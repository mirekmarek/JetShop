<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\MVC_Layout;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\EMail_Sent;
use JetApplication\ReturnOfGoods;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_DateStarted() );
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
			'status_code'
		]);
		
	}

	public function add_Action() : void
	{
	}
	
	public function edit_main_Action() : void
	{
		/**
		 * @var ReturnOfGoods $return
		 */
		$return = $this->current_item;
		
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
			Tr::_( 'Return of goods <b>%NUMBER%</b>', [ 'NUMBER' => $return->getNumber() ] )
		);
		
		$this->view->setVar( 'return', $return );
		$this->view->setVar('listing', $this->getListing());
		
		Handler::initHandlers( $this->view, $return );
		Handler::handleHandlers();
		
		$this->output( 'edit' );

	}
	
	
}