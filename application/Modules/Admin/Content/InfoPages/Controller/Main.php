<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\InfoPages;


use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function getEntityNameReadable() : string
	{
		return 'Info Page';
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