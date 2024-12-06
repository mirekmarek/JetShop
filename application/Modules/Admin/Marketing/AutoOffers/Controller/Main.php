<?php
namespace JetApplicationModule\Admin\Marketing\AutoOffers;

use JetApplication\Admin_EntityManager_Marketing_Controller;

class Controller_Main extends Admin_EntityManager_Marketing_Controller
{
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Offer() );
		$this->listing_manager->addFilter( new Listing_Filter_Offer() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'active_state',
			'internal_name',
			'internal_code',
			'offer_product_id',
			'valid_from',
			'valid_till',
			'internal_notes'
		]);
	}

}