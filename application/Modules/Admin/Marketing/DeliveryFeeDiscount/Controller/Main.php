<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Marketing\DeliveryFeeDiscount;

use JetApplication\Admin_EntityManager_Marketing_Controller;



/**
 *
 */
class Controller_Main extends Admin_EntityManager_Marketing_Controller
{
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addFilter( new Listing_Filter_DeliveryMethod() );
		$this->listing_manager->addColumn( new Listing_Column_DeliveryMethod() );
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'shop',
			'active_state',
			'delivery_method',
			'internal_name',
			'internal_code',
			'valid_from',
			'valid_till',
			'internal_notes'
		]);
	}
	
	
}