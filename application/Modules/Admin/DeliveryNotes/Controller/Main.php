<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\DeliveryNotes;

use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\UI_messages;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_EShopEntity_Listing;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use JetApplication\Invoices;
use JetApplication\DeliveryNote;


class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	protected ?DeliveryNote $invoice = null;
	
	protected ?Admin_Managers_EShopEntity_Listing $listing_manager = null;
	
	
	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->invoice = DeliveryNote::get((int)$id));
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
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_Total() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		$this->listing_manager->addColumn( new Listing_Column_Date() );
		
		$this->listing_manager->addFilter( new Listing_Filter_Date() );
		$this->listing_manager->addFilter( new Listing_Filter_Customer() );
		$this->listing_manager->addFilter( new Listing_Filter_Product() );
		
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			
			$search_separated = explode( ' ', $search );
			
			$q = [];
			
			if(count( $search_separated )==2) {
				$search_query_alt = addslashes($search_separated[1].' '.$search_separated[0]);
				$q[] = [
					'customer_first_name *' => '%'.$search_separated[0].'%',
					'AND',
					'customer_surname *' => '%'.$search_separated[1].'%'
				];
				$q[] = 'OR';
				$q[] = [
					'customer_first_name *' => '%'.$search_separated[1].'%',
					'AND',
					'customer_surname *' => '%'.$search_separated[0].'%'
				];
				
			}
			
			$q[] = 'OR';
			$q['customer_phone *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['customer_email *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['customer_company_name *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['customer_company_id *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['number'] = $search;
			
			return $q;
		} );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			Listing_Column_Number::KEY,
			Listing_Column_Customer::KEY,
			Listing_Column_Total::KEY,
			Listing_Column_Items::KEY,
			Listing_Column_Date::KEY,
		]);
		
		/*
		$this->listing_manager->setCustomBtnRenderer( function() : string {
			return $this->view->render('list/toolbar');
		} );
		*/
	}
	
	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$output = $this->getListing()->renderListing();
		
		$this->content->output( $output );
	}
	
	
	public function add_Action() : void
	{
	}
	
	public function edit_Action() : void
	{
		/**
		 * @var DeliveryNote $invoice
		 */
		$invoice = $this->invoice;
		
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Delivery note <b>%NUMBER%</b>', [ 'NUMBER' => $invoice->getNumber() ] )
		);
		
		$GET = Http_Request::GET();
		
		if($GET->getString('handle')=='show_pdf') {
			$pdf = Invoices::generateDeliveryNotePDF( $invoice );
			
			$file_name = 'delivery_note_'.$invoice->getNumber().'.pdf';
			
			Http_Headers::sendDownloadFileHeaders(
				file_name: $file_name,
				file_mime: 'application/pdf',
				file_size: strlen($pdf),
				force_download: false
			);
			
			echo $pdf;
			
			Application::end();
		}
		
		if($GET->getString('handle')=='cancel') {
			$invoice->cancel();
			
			UI_messages::info( Tr::_('Delivery note has been send cancelled') );
			
			Http_Headers::reload(unset_GET_params: ['handle']);
		}
		
		if($GET->getString('handle')=='send') {
			Invoices::sendDeliveryNote( $invoice );
			
			UI_messages::success( Tr::_('Delivery note has been send to the customer') );
			
			Http_Headers::reload(unset_GET_params: ['handle']);
		}
		
		
		$this->view->setVar( 'invoice', $invoice );
		$this->view->setVar( 'listing', $this->getListing());
		
		

		
		$this->output( 'edit' );
	}
	
	
}