<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Discounts\CodesDefinition;


use JetApplication\Admin_EntityManager_Controller;



class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable(): string
	{
		return 'Discount code definition';
	}
	
	
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
			'eshop',
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