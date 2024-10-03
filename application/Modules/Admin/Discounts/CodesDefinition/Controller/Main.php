<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;

use JetApplication\Admin_EntityManager_Marketing_Controller;


/**
 *
 */
class Controller_Main extends Admin_EntityManager_Marketing_Controller
{
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn( new Listing_Column_Code() );
		$this->listing_manager->addColumn( new Listing_Column_InternalNotes() );
		$this->listing_manager->addColumn( new Listing_Column_DiscountType() );
		$this->listing_manager->addColumn( new Listing_Column_MinimalOrderAmount() );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ) : array {
			$search = '%'.$search.'%';
			
			return [
				'code *' => $search,
				'OR',
				'internal_notes *' => $search,
				'OR',
				'internal_name *' => $search,
			];
		} );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'shop',
			'active_state',
			'code',
			'internal_notes',
			'offer_product_id',
			'valid_from',
			'valid_till',
			'internal_notes',
			Listing_Column_DiscountType::KEY,
			Listing_Column_MinimalOrderAmount::KEY,
		]);
		
		
	}
}