<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderDispatch\Overview;

use Jet\Http_Headers;
use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Entity_Listing;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
use Jet\Navigation_Breadcrumb;
use JetApplication\Order;
use JetApplication\OrderDispatch;


class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	protected ?OrderDispatch $order_dispatch = null;
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;


	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->order_dispatch = OrderDispatch::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => '',
					'edit'   => '',
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
	
	public function getListing() : Admin_Managers_Entity_Listing
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
		$this->listing_manager->addColumn( new Listing_Column_Warehouse() );
		$this->listing_manager->addColumn( new Listing_Column_Created() );
		$this->listing_manager->addColumn( new Listing_Column_DispatchDate() );
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		$this->listing_manager->addColumn( new Listing_Column_Recipient() );
		$this->listing_manager->addColumn( new Listing_Column_Order() );
		$this->listing_manager->addColumn( new Listing_Column_Context() );
		$this->listing_manager->addColumn( new Listing_Column_Carrier() );
		$this->listing_manager->addColumn( new Listing_Column_TrackingNumber() );
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Warehouse() );
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_Carrier() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search_separated = explode( ' ', $search );
			
			$q = [];
			
			if(count( $search_separated )==2) {
				$search_query_alt = addslashes($search_separated[1].' '.$search_separated[0]);
				$q[] = [
					'recipient_first_name *' => '%'.$search_separated[0].'%',
					'AND',
					'recipient_surname *' => '%'.$search_separated[1].'%'
				];
				$q[] = 'OR';
				$q[] = [
					'recipient_first_name *' => '%'.$search_separated[1].'%',
					'AND',
					'recipient_surname *' => '%'.$search_separated[0].'%'
				];
				
			}
			
			$q[] = 'OR';
			$q['recipient_phone *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['recipient_email *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['recipient_company *'] = '%'.$search.'%';
			$q[] = 'OR';
			$q['number'] = $search;
			$q[] = 'OR';
			$q['tracking_number'] = $search;
			
			$orders = Order::dataFetchCol(select: ['id'], where: ['number'=>$search], raw_mode: true);
			if($orders) {
				$q[] = 'OR';
				$q['order_id'] = $orders;
			}
			
			return $q;
			
		} );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			'created',
			'dispatch_date',
			'number',
			'status',
			'recipient',
			'order_number',
			'context',
			'carrier',
			'tracking_number'
		]);
		
	}
	
	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$this->content->output( $this->getListing()->renderListing() );
	}


	public function add_Action() : void
	{
	}
	
	public function edit_Action() : void
	{
		Http_Headers::movedTemporary( $this->order_dispatch->getEditUrl() );
	}
	
	
}