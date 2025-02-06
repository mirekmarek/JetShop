<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Invoices;


use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\Invoices;
use JetApplication\Invoice;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupRouter( string $action, string $selected_tab ) : void
	{
		parent::setupRouter( $action, $selected_tab );
		
		
		$this->router->addAction('create_correction_invoice')
			->setResolver( function() use ($action) : bool {
				return $this->current_item && $action=='create_correction_invoice';
			} );
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
	
	
	public function add_Action() : void
	{
	}
	
	public function edit_main_Action() : void
	{
		/**
		 * @var Invoice $invoice
		 */
		$invoice = $this->current_item;
		
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Invoice <b>%NUMBER%</b>', [ 'NUMBER' => $invoice->getNumber() ] )
		);
		
		$GET = Http_Request::GET();
		
		if($GET->getString('handle')=='show_pdf') {
			$pdf = Invoices::generateInvoicePDF( $invoice );
			
			$file_name = 'invoice_'.$invoice->getNumber().'.pdf';
			
			Http_Headers::sendDownloadFileHeaders(
				file_name: $file_name,
				file_mime: 'application/pdf',
				file_size: strlen($pdf),
				force_download: false
			);
			
			echo $pdf;
			
			Application::end();
		}
		
		
		if($GET->getString('handle')=='send') {
			Invoices::sendInvoice( $invoice );
			
			UI_messages::success( Tr::_('Invoice has been send to the customer') );
			
			Http_Headers::reload(unset_GET_params: ['handle']);
		}
		
		$this->view->setVar( 'invoice', $invoice );
		$this->view->setVar( 'listing', $this->getListing());
		
		

		
		$this->output( 'edit' );
	}
	
	
	public function create_correction_invoice_Action() : void
	{
		/**
		 * @var Invoice $invoice
		 */
		$invoice = $this->current_item;
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Create correction invoice for <b>%NUMBER%</b>', [ 'NUMBER' => $invoice->getNumber() ] )
		);
		
		if( ($correction_invoice=$invoice->catchCorrectionInvoiceForm()) ) {
			UI_messages::success( Tr::_('Correction invoice has been created') );
			
			Http_Headers::reload(
				set_GET_params: ['id'=>$correction_invoice->getId()],
				unset_GET_params: ['action']
			);
			
		}
		
		$this->view->setVar('invoice', $invoice);
		
		$this->output('create_correction_invoice');
	}
	
}