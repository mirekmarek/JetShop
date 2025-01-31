<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Marketing\GiftProduct;


use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Gift for product';
	}
	
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