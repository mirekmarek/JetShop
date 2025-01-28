<?php
namespace JetApplicationModule\Admin\Marketing\ProductStickers;

use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable(): string
	{
		return 'Product sticker';
	}
	
	public function setupListing(): void
	{
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'active_state',
			'internal_name',
			'internal_code',
			'valid_from',
			'valid_till',
			'internal_notes'
		]);
	}

}