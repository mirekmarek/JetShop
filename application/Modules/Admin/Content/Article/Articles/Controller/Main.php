<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;

use Jet\Tr;
use JetApplication\Admin_EntityManager_Controller;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getTabs(): array
	{
		$tabs = parent::getTabs();
		
		if(isset($tabs['description'])) {
			$tabs['description'] = Tr::_('Content');
		}
		
		return $tabs;
	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Author() );
		$this->listing_manager->addColumn( new Listing_Column_KindOfArticle() );
		
		$this->listing_manager->addFilter( new Listing_Filter_KindOfArticle() );
		$this->listing_manager->addFilter( new Listing_Filter_Author() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'internal_name',
			'internal_code',
			'internal_notes',
			Listing_Column_Author::KEY,
			Listing_Column_KindOfArticle::KEY
		]);
		
	}
	
}