<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;

use Jet\AJAX;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\EShops;

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
	
	public function edit_description_Action() : void
	{
		$GET = Http_Request::GET();
		if($GET->exists('show_categories')) {
			$show_categories = $GET->getString('show_categories');
			$show_categories = $show_categories ? explode(',', $show_categories) : [];
			$eshop = EShops::get( $GET->getString('eshop') );
			
			$item = $this->current_item;
			
			$this->view->setVar('item', $item);
			$this->view->setVar('category_ids', $show_categories);
			$this->view->setVar('eshop', $eshop);
			
			AJAX::snippetResponse( $this->view->render('edit/main/categories') );
			
		}
		
		parent::edit_description_Action();
	}
}