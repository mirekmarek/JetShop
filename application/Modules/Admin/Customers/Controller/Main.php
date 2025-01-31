<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Customers;


use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function getEntityNameReadable(): string
	{
		return 'Customer';
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
	
	public function edit_main_Action() : void
	{
		$customer = $this->current_item;
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Customer detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $customer->getEmail() ] )
		);
		
		$this->view->setVar( 'customer', $customer );
		
		$this->output( 'edit' );
	}

}