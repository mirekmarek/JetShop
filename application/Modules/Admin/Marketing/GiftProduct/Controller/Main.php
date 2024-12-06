<?php
namespace JetApplicationModule\Admin\Marketing\GiftProduct;

use JetApplication\Admin_EntityManager_Marketing_Controller;

class Controller_Main extends Admin_EntityManager_Marketing_Controller
{
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Gift() );
		$this->listing_manager->addFilter( new Listing_Filter_Gift() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'active_state',
			'internal_name',
			'internal_code',
			'gift_product_id',
			'valid_from',
			'valid_till',
			'internal_notes'
		]);
	}
	
}