<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Customers;

use JetApplication\Admin_Managers;
use JetApplication\Admin_Managers_EShopEntity_Listing;
use JetApplication\Customer as Customer;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	protected ?MVC_Controller_Router_AddEditDelete $router = null;

	protected ?Customer $customer = null;
	
	protected ?Admin_Managers_EShopEntity_Listing $listing_manager = null;
	
	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->customer = Customer::get($id));
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => Main::ACTION_GET,
					'add'    => '',
					'edit'   => Main::ACTION_UPDATE,
					'delete' => '',
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
		$this->listing_manager->addColumn( new Listing_Column_Email() );
		$this->listing_manager->addColumn( new Listing_Column_Name() );
		$this->listing_manager->addColumn( new Listing_Column_Phone() );
		$this->listing_manager->addColumn( new Listing_Column_Registration() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search = '%'.$search.'%';
			
			return [
				'id *'            => $search,
				'OR',
				'email *' => $search,
			];
			
		} );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'eshop',
			'id',
			'email',
			'name',
			'phone_number',
			'registration_date_time'
		]);

	}
	

	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$this->content->output( $this->getListing()->renderListing() );
	}


	public function edit_Action() : void
	{
		$this->view_Action();
	}

	public function view_Action() : void
	{
		$customer = $this->customer;

		$this->setBreadcrumbNavigation(
			Tr::_( 'Customer detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $customer->getEmail() ] )
		);

		$this->view->setVar( 'customer', $customer );

		$this->output( 'edit' );
	}


}