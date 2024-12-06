<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Content\InfoBoxes;

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
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'internal_name',
			'internal_code',
			'internal_notes'
		]);
	}
	
}