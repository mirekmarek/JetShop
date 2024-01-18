<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\OrderStatus;

use JetApplication\Admin_Entity_WithShopData_Manager_Controller;

class Controller_Main extends Admin_Entity_WithShopData_Manager_Controller
{
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Kind() );
		$this->listing_manager->addColumn( new Listing_Column_IsDefault() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'kind',
			'is_default',
			'internal_name',
			'internal_code',
			'internal_notes'
		]);
	}
}