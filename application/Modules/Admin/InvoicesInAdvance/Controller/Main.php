<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\InvoicesInAdvance;


use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\InvoiceInAdvance;
use JetApplication\Invoices;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing() : void
	{
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
			'number',
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
	
	
	public function add_Action() : void
	{
	}
	
	public function edit_main_Action() : void
	{
		/**
		 * @var InvoiceInAdvance $invoice
		 */
		$invoice = $this->current_item;
		
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Invoice in advance <b>%NUMBER%</b>', [ 'NUMBER' => $invoice->getNumber() ] )
		);
		
		$GET = Http_Request::GET();
		
		if($GET->getString('handle')=='show_pdf') {
			$pdf = Invoices::generateInvoiceInAdvancePDF( $invoice );
			
			$file_name = 'invoice_in_advance_'.$invoice->getNumber().'.pdf';
			
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
			
			UI_messages::info( Tr::_('Invoice in advance has been send cancelled') );
			
			Http_Headers::reload(unset_GET_params: ['handle']);
		}
		
		if($GET->getString('handle')=='send') {
			Invoices::sendInvoiceInAdvance( $invoice );
			
			UI_messages::success( Tr::_('Invoice in advance has been send to the customer') );
			
			Http_Headers::reload(unset_GET_params: ['handle']);
		}
		
		
		$this->view->setVar( 'invoice', $invoice );
		$this->view->setVar( 'listing', $this->getListing());
		
		

		
		$this->output( 'edit' );
	}
	
	
}