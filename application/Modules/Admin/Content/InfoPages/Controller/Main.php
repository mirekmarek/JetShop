<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoPages;

use Jet\Tr;
use JetApplication\Admin_EntityManager_WithEShopData_Controller;

class Controller_Main extends Admin_EntityManager_WithEShopData_Controller
{
	protected function getTabs() : array
	{
		return [
			'main'   => Tr::_( 'Main data' ),
		];
	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Page() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'page_id',
			'internal_name',
			'internal_code',
			'internal_notes'
		]);
	}
	
}