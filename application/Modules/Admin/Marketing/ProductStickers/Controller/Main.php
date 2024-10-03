<?php
namespace JetApplicationModule\Admin\Marketing\ProductStickers;

use JetApplication\Admin_EntityManager_Marketing_Controller;

class Controller_Main extends Admin_EntityManager_Marketing_Controller
{
	public function setupListing(): void
	{
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'shop',
			'active_state',
			'internal_name',
			'internal_code',
			'valid_from',
			'valid_till',
			'internal_notes'
		]);
	}

}