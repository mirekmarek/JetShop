<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Content\Article\Articles;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Content_Article;

class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Article';
	}
	
	
	public function getTabs(): array
	{
		$tabs = parent::getTabs();
		$tabs['categories'] = Tr::_('Categories');

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
	
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_categories', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='categories' && $action=='';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'categories'], ['action'] );
			});
		
	}
	
	public function edit_categories_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_( 'Categories' ) );
		
		$this->view->setVar( 'item', $this->current_item);
		$this->view->setVar( 'editable', $this->current_item->isEditable() );
		
		if( $this->current_item->isEditable() ) {
			/**
			 * @var Content_Article $article
			 */
			$article = $this->current_item;
			$GET = Http_Request::GET();
			if(($add=$GET->getInt('add_category'))) {
				$article->addCategory( $add );
				Http_Headers::reload(unset_GET_params: ['add_category']);
			}
			
			if(($remove=$GET->getInt('remove_category'))) {
				$article->removeCategory( $remove );
				Http_Headers::reload(unset_GET_params: ['remove_category']);
			}
			
			if(($sort_categories=$GET->getString('sort_categories'))) {
				$article->sortCategories( explode(',', $sort_categories) );
				Http_Headers::reload(unset_GET_params: ['sort_categories']);
			}
			
			
			
		}
		
		$this->output('edit/categories');
	}
}