<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Customers;


use JetApplication\Admin_EntityManager_Controller;
use Jet\Tr;
use JetApplication\Customer_Address;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Email() );
		$this->listing_manager->addColumn( new Listing_Column_Name() );
		$this->listing_manager->addColumn( new Listing_Column_Phone() );
		$this->listing_manager->addColumn( new Listing_Column_Registration() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			
			$search_separated = explode( ' ', $search );
			$search = '%'.$search.'%';
			
			$q = [];
			
			$q = [
				'company_name *' => $search,
				'OR',
				'company_id' => $search,
				'OR',
				'company_vat_id' => $search,
			];
			
			if(count( $search_separated )==2) {
				$q[] = 'OR';
				$q[] = [
					'first_name *' => '%'.$search_separated[0].'%',
					'AND',
					'surname *' => '%'.$search_separated[1].'%'
				];
				$q[] = 'OR';
				$q[] = [
					'first_name *' => '%'.$search_separated[1].'%',
					'AND',
					'surname *' => '%'.$search_separated[0].'%'
				];
			}
			
			
			$by_address = Customer_Address::dataFetchCol(
				select: ['customer_id'],
				where: $q
			);
			
			$q = [
				'id *'            => $search,
				'OR',
				'email *' => $search,
			];
			
			if($by_address) {
				$q[] = 'OR';
				$q['id'] = $by_address;
			}
			
			if(count( $search_separated )==2) {
				$q[] = 'OR';
				$q[] = [
					'first_name *' => '%'.$search_separated[0].'%',
					'AND',
					'surname *' => '%'.$search_separated[1].'%'
				];
				$q[] = 'OR';
				$q[] = [
					'first_name *' => '%'.$search_separated[1].'%',
					'AND',
					'surname *' => '%'.$search_separated[0].'%'
				];
			}
			
			
			return $q;
			
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
		$this->view->setVar( 'edit_manager', $this->getEditorManager() );
		
		$this->output( 'edit' );
	}

}