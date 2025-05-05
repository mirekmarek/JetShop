<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Orders;


use Jet\Http_Request;
use Jet\MVC_Layout;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\Admin_Managers;
use JetApplication\Order;
use JetApplication\OrderDispatch;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing() : void
	{
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
			
			$by_tracking_number = OrderDispatch::findOrderIdsByTrackingNumber( $search );
			if($by_tracking_number) {
				$q[] = 'OR';
				$q['id'] = $by_tracking_number;
			}
			
			return $q;
		} );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			'number',
			Listing_Column_Customer::KEY,
			Listing_Column_TotalAmount::KEY,
			Listing_Column_Items::KEY,
			Listing_Column_DatePurchased::KEY,
			'status'
		]);
		
		$this->listing_manager->setCustomBtnRenderer( function() : string {
			return $this->view->render('list/toolbar');
		} );
		
		$this->listing_manager->addHandler( new Listing_Handler_DispatchAllReady() );
		
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
		
		if(($sent_email=Admin_Managers::EntityEdit()->handleShowSentEmail( $order ))) {
			$this->content->output( $sent_email );
			return;
		}
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Order <b>%NUMBER%</b>', [ 'NUMBER' => $order->getNumber() ] )
		);
		
		$this->view->setVar( 'order', $order );
		$this->view->setVar('listing', $this->getListing());
		
		Plugin::initPlugins( $this->view, $order );
		$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
		
		if(Main::getCurrentUserCanEdit()) {
			Plugin::handlePlugins();
		}
		
		if(Http_Request::GET()->exists('print')) {
			MVC_Layout::getCurrentLayout()->setScriptName('plain');
			$this->output( 'print' );
		} else {
			$this->output( 'edit' );
		}
	}
	
	
}