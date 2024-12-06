<?php
namespace JetApplicationModule\Admin\Catalog\Signposts;


use Jet\Application;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_WithEShopData_Controller;
use JetApplication\Signpost;


class Controller_Main extends Admin_EntityManager_WithEShopData_Controller
{
	public function getTabs(): array
	{
		$tabs = parent::getTabs();
		
		$tabs['categories'] = Tr::_('Categories');
		
		return $tabs;
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		$this->router->addAction('sort_signposts', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return Http_Request::GET()->exists('sort_signposts');
			} );
		
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_categories', Main::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='categories';
			} );
		
		
	}
	
	public function setupListing() : void
	{
		
		$this->listing_manager->addColumn( new Listing_Column_Priority() );
		$this->listing_manager->addColumn( new Listing_Column_InternalName() );
		

		$this->listing_manager->setDefaultColumnsSchema([
			'id|active_state',
			'priority',
			'internal_name',
			'internal_code',
			'internal_notes',
		]);
		
		$this->listing_manager->setCustomBtnRenderer( function() : string {
			if(!Main::getCurrentUserCanEdit()) {
				return '';
			}
			
			return $this->view->render('sort-signposts');
		} );

		
	}
	
	
	public function edit_categories_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Categories') );
		
		$signpost = $this->current_item;
		
		$this->view->setVar('signpost', $this->current_item);
		$this->view->setVar('editable', $signpost->isEditable());
		
		
		$_updated = function() use ($signpost) {
			
			UI_messages::success(
				Tr::_( 'Signpost <b>%NAME%</b> has been updated', [ 'NAME' => $signpost->getInternalName() ] )
			);
			
		};
		
		
		/**
		 * @var Signpost $signpost
		 */
		if($signpost->isEditable()) {
			
			$GET = Http_Request::GET();
			
			if(($add_product_id=$GET->getInt('add_category'))) {
				if($signpost->addCategory( $add_product_id )) {
					$_updated();
				}
				Http_Headers::reload(unset_GET_params: ['add_category']);
			}
			
			if(($remove_category_id=$GET->getInt('remove_category'))) {
				if($signpost->removeCategory( $remove_category_id )) {
					$_updated();
				}
				Http_Headers::reload(unset_GET_params: ['remove_category']);
			}
			
			if(($sort_categories=$GET->getString('sort_categories'))) {
				
				$signpost->sortCategories( explode(',', $sort_categories) );
				Application::end();
			}
			
		}
		
		
		$this->output('edit/categories');
	}
	
	public function sort_signposts_Action() : void
	{
		$sort = explode(',', Http_Request::GET()->getString('sort_signposts'));
		
		$p = 0;
		foreach($sort as $id) {
			$sp = Signpost::get( $id );
			if($sp) {
				$sp->setPriority( $p );
				$sp->save();
				$p++;
			}
		}
		
		Http_Headers::reload(unset_GET_params: ['sort_signposts']);
	}
}