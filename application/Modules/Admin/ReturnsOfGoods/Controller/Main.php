<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\ReturnsOfGoods;


use Jet\AJAX;
use Jet\Data_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\Application_Service_Admin;
use JetApplication\EShops;
use JetApplication\Order;
use JetApplication\Product;
use JetApplication\ReturnOfGoods;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing() : void
	{
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
		
		$this->current_item = new ReturnOfGoods();
		
		$this->setBreadcrumbNavigation( Tr::_('Create new') );
		
		
		$form = $this->current_item->getAddForm();
		
		if( $this->current_item->catchAddForm() ) {
			$this->current_item->setDateStarted( Data_DateTime::now() );
			$this->current_item->save();
			$this->current_item->newReturnOfGoodsFinished();
			
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
		 * @var ReturnOfGoods $return
		 */
		$return = $this->current_item;
		
		if(($sent_email=Application_Service_Admin::EntityEdit()->handleShowSentEmail( $return ))) {
			$this->content->output( $sent_email );
			return;
		}
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Return of goods <b>%NUMBER%</b>', [ 'NUMBER' => $return->getNumber() ] )
		);
		
		$this->view->setVar( 'return', $return );
		$this->view->setVar('listing', $this->getListing());
		
		Plugin::initPlugins( $this->view, $return );
		$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
		
		if(Main::getCurrentUserCanEdit()) {
			Plugin::handlePlugins();
		}
		
		$this->output( 'edit' );

	}
	
	
}