<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Complaints;

use Jet\AJAX;
use Jet\Data_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Logger;
use Jet\MVC_Layout;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\Complaint;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\Product;



class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_DateStarted() );
		$this->listing_manager->addColumn( new Listing_Column_Type() );
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Product() );
		$this->listing_manager->addFilter( new Listing_Filter_Type() );
		$this->listing_manager->addFilter( new Listing_Filter_StartedDate() );
		$this->listing_manager->addFilter( new Listing_Filter_FinishedDate() );
		
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
				$q[] = 'OR';
				$q[] = [
					'delivery_first_name *' => '%'.$search_separated[1].' '.$search_separated[0].'%',
				];
				$q[] = 'OR';
				$q[] = [
					'delivery_first_name *' => '%'.$search_separated[0].' '.$search_separated[1].'%',
				];
				
			}
			
			$q[] = 'OR';
			$q['phone *'] = '%'.$search;
			$q[] = 'OR';
			$q['delivery_first_name *'] = '%'.strtolower($search).'%';
			$q[] = 'OR';
			$q['delivery_surname *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['email *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['delivery_company_name *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['number'] = $search;
			
			
			$products = Application_Service_Admin::FulltextSearch()->search(
				Product::getEntityType(),
				$search,
			);
			
			if($products) {
				$q[] = 'OR';
				$q['product_id'] = $products;
			}
			
			if(is_numeric($search)) {
				$order_id = Order::dataFetchCol(select: ['id'], where: ['number' => $search]);
				if($order_id) {
					$q[] = 'OR';
					$q['order_id'] = $order_id;
				}
				
			}
			$q[] = 'OR';
			$q['bill_number'] = $search;
			
			
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
		$GET = Http_Request::GET();
		if(
			($order_number = $GET->getString('get_order_info')) &&
			($eshop_key = $GET->getString('eshop', valid_values: array_keys(EShops::getList())))
		) {
			
			$eshop = EShops::get($eshop_key);
			
			$order = Order::getByNumber( $order_number, $eshop );
			
			if(!$order) {
				AJAX::operationResponse(success: false);
			} else {
				AJAX::operationResponse(success: true, data: $order->jsonSerialize() );
			}
			
		}
		
		$this->current_item = new Complaint();
		
		$this->setBreadcrumbNavigation( Tr::_('Create new') );
		
		
		$form = $this->current_item->getAddForm();
		
		if( $this->current_item->catchAddForm() ) {
			$this->current_item->setDateStarted( Data_DateTime::now() );
			$this->current_item->save();
			
			$this->current_item->newComplaintFinished();
			
			UI_messages::success( $this->generateText_add_msg() );
			
			Http_Headers::reload(
				set_GET_params: ['id'=>$this->current_item->getId()],
				unset_GET_params: ['action']
			);
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $this->current_item );
		
		
		$this->content->output(
			$this->getEditorManager()->renderAdd( $form )
		);
		
	}
	
	public function edit_main_Action() : void
	{
		/**
		 * @var Complaint $complaint
		 */
		$complaint = $this->current_item;
		
		if(Main::getCurrentUserCanEdit()) {
			if(($delete_image_id=Http_Request::GET()->getInt('delete_image'))) {
				foreach($complaint->getImages() as $image) {
					if($image->getId() == $delete_image_id) {
						$image->delete();
						UI_messages::success( Tr::_('Image %IMAGE% has been deleted', ['IMAGE'=>$image->getName()]) );
						
						Logger::info(
							event: 'complaint:image_deleted',
							event_message: 'Image '.$image->getName().' has been deleted',
							context_object_id: $complaint->getId(),
							context_object_name: $complaint->getNumber().':'.$image->getName()
						);
						break;
					}
				}
				
				Http_Headers::reload(unset_GET_params: ['delete_image']);
			}
		}
		
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
		
		if(Http_Request::GET()->exists('print')) {
			MVC_Layout::getCurrentLayout()->setScriptName('plain');
			$this->output( 'print' );
		} else {
			$this->output( 'edit' );
		}
		

	}
	
	
}