<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Orders;

use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Order as Order;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;



class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router_AddEditDelete $router = null;
	protected ?Order $order = null;
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;


	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->order = Order::get((int)$id));
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
		Admin_Managers::UI()->initBreadcrumb();

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
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Customer() );
		$this->listing_manager->addColumn( new Listing_Column_TotalAmount() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		$this->listing_manager->addColumn( new Listing_Column_DatePurchased() );
		$this->listing_manager->addColumn( new Listing_Column_StatusId() );
		
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_Delivery() );
		$this->listing_manager->addFilter( new Listing_Filter_Payment() );
		$this->listing_manager->addFilter( new Listing_Filter_DatePurchased() );
		$this->listing_manager->addFilter( new Listing_Filter_Customer() );

		//TODO: filter product
		//TODO: filter source
		
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search = '%'.$search.'%';
		
			//TODO: better search
			return [
				'id *'            => $search,
				'OR',
				'number *'            => $search,
				'OR',
				'email *' => $search,
			];
			
		} );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'shop',
			'number',
			'customer',
			'total_amount',
			'items',
			'date_purchased',
			'status_id'
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
		$order = $this->order;
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Order <b>%NUMBER%</b>', [ 'NUMBER' => $order->getNumber() ] )
		);
		
		$this->view->setVar( 'order', $order );
		$this->view->setVar('listing', $this->getListing());
		$this->output( 'edit' );

	}
	
	public function view_Action() : void
	{
		$this->edit_Action();
	}

}