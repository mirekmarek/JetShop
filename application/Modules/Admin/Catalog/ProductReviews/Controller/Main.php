<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\ProductReviews;

use JetApplication\Admin_EntityManager_Controller;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_Rank() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorName() );
		$this->listing_manager->addColumn( new Listing_Column_AuthorEmail() );
		$this->listing_manager->addColumn( new Listing_Column_Created() );
		$this->listing_manager->addColumn( new Listing_Column_Source() );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'eshop',
			'product_id',
			'status',
			'created',
			'author_name',
			'author_email',
			'rank',
			'source'
		]);
	}

	protected function edit_main_initPlugins(): void
	{
		if(Main::getCurrentUserCanEdit()) {
			Plugin::initPlugins( $this->view, $this->current_item );
			
			$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
			
			Plugin::handlePlugins();
		}
	}
}