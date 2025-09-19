<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Categories;


use Jet\Application;
use Jet\Data_Tree;
use Jet\MVC_Controller_Router;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Application_Service_Admin;
use JetApplication\Application_Admin;
use JetApplication\Category;

use Jet\UI_messages;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Data_Tree_Node;



class Controller_Main extends Admin_EntityManager_Controller
{
	
	protected ?MVC_Controller_Router $router = null;

	protected ?Data_Tree $tree = null;
	
	protected function getCustomTabs() : array
	{
		if(!$this->current_item) {
			return [];
		}
		
		/**
		 * @var Category $current_item
		 */
		$current_item = $this->current_item;
		
		$tabs['products'] = Tr::_( 'Products (%count%)', ['count'=>count($this->current_item->getProductIds())] );
		
		return $tabs;
		
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->setDefaultAction(  'default', Main::ACTION_GET  );
		
		$this->router->addAction( 'add', Main::ACTION_ADD )->setResolver(function() use ($action) {
			return ( $action=='create' );
		});
		
		$this->router->addAction( 'save_sort', Main::ACTION_GET )->setResolver(function() use ($action, $selected_tab) {
			return $action=='save_sort';
		});
		
		$this->router->addAction( 'edit', Main::ACTION_GET )
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='main';
			})
			->setURICreator(function( int $id ) : string {
				return Http_Request::currentURI(['id'=>$id],['action']);
			} );
		
		$this->router->addAction( 'edit_images', Main::ACTION_GET )->setResolver(function() use ($action, $selected_tab) {
			return $this->current_item && $selected_tab=='images';
		});
		
		$this->router->addAction( 'edit_description', Main::ACTION_GET )->setResolver(function() use ($action, $selected_tab) {
			return $this->current_item && $selected_tab=='description';
		});
		
		
		$this->router->addAction( 'edit_products', Main::ACTION_GET )->setResolver(function() use ($action, $selected_tab) {
			return $this->current_item && $selected_tab=='products';
		});
		
	}
	
	
	public function getCurrentItem() : Category|null
	{
		return $this->current_item;
	}


	public function getCurrentCategoryId() : int
	{
		if(!$this->current_item) {
			return 0;
		}

		return $this->current_item->getId();
	}
	
	public function initTree() : Data_Tree
	{
		if(!$this->tree) {
			$sort_scope = [
				Category::SORT_PRIORITY => Tr::_('priority'),
				Category::SORT_NAME     => Tr::_('name'),
			];
			
			$active_filter_scope = [
				'' => Tr::_('all'),
				'active' => Tr::_('only active'),
				'non_active' => Tr::_('only non-active'),
			];
			
			
			$GET = Http_Request::GET();
			
			$sort = $GET->getString('sort', Category::SORT_PRIORITY, array_keys($sort_scope));
			$active_filter = $GET->getString('active', '', array_keys($active_filter_scope));
			
			/*
			$_active_filter = match( $active_filter ) {
				'' => null,
				'active' => true,
				'non_active' => false
			};
			*/
			
			$_active_filter = null;
			
			$this->view->setVar('allow_to_sort', $sort==Category::SORT_PRIORITY);
			
			$this->view->setVar('sort_scope', $sort_scope);
			$this->view->setVar('sort', $sort );
			
			$this->view->setVar('active_filter_scope', $active_filter_scope);
			$this->view->setVar('active_filter', $active_filter );

			
			$this->tree = Category::getTree( $sort, $_active_filter );
		}
		
		return $this->tree;
	}
	
	public function getCurrentNode() : ?Data_Tree_Node
	{
		return $this->initTree()->getNode( $this->getCurrentCategoryId()?:'' );
	}
	
	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		$tree = $this->initTree();
		
		if(!$this->getCurrentCategoryId()) {
			return;
		}
		
		$current_node = $tree->getNode( $this->getCurrentCategoryId() );

		
		foreach( $current_node->getPath() as $node ) {

			if(!$node->getId()) {
				continue;
			}

			$label = $node->getLabel();

			if(!$label) {
				$label = '???';
			}
			
			Navigation_Breadcrumb::addURL( $label, $this->getControllerRouter()->action('edit')->URI($node->getId()) );

		}

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}



	public function listing_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$this->output( 'default' );
	}
	
	
	public function edit_main_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit' ) );
		$category = $this->getCurrentItem();
		
		if($category->isEditable()) {
			$this->edit_main_handleActivation();
			
			$GET = Http_Request::GET();
			
			if(($target_id=$GET->getInt('move_category'))) {
				$category->move( $target_id );
				Http_Headers::reload(unset_GET_params: ['move_category']);
			}
			if(($target_id=$GET->getInt('move_subcategories'))) {
				$category->moveSubcategories( $target_id );
				Http_Headers::reload(unset_GET_params: ['move_subcategories']);
			}
			
			if($GET->getString('action')=='change_kind_of_product') {
				$category->setKindOfProductId( Http_Request::POST()->getInt('kind_of_product_id') );
				
				UI_messages::success(
					Tr::_( 'Category <b>%NAME%</b> has been updated', [ 'NAME' => $category->getPathName() ] )
				);
				
				Http_Headers::reload( unset_GET_params: ['action'] );
			}
			
			if( $category->catchEditMainForm() ) {
				$category->save();
				
				UI_messages::success(
					Tr::_( 'Category <b>%NAME%</b> has been updated', [ 'NAME' => $category->getPathName() ] )
				);
				
				Http_Headers::reload();
				
			}
		}
		
		$this->view->setVar('form', $category->getEditMainForm() );
		$this->view->setVar('toolbar', 'edit/main/toolbar');
		
		
		$this->output( 'edit/main' );
	}
	
	public function edit_description_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit' ) );
		$category = $this->getCurrentItem();
		
		if($category->isEditable()) {
			if( $category->catchDescriptionEditForm() ) {
				$category->save();
				
				UI_messages::success(
					Tr::_( 'Category <b>%NAME%</b> has been updated', [ 'NAME' => $category->getPathName() ] )
				);
				
				Http_Headers::reload();
				
			}
		}
		
		$this->view->setVar('form', $category->getDescriptionEditForm() );
		$this->view->setVar('toolbar', 'edit/main/toolbar');
		
		
		$this->output( 'edit/description' );
	}
	
	
	public function edit_products_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Products' ) );
		$category = $this->getCurrentItem();
		
		
		$_updated = function() use ($category) {
			
			UI_messages::success(
				Tr::_( 'Category <b>%NAME%</b> has been updated', [ 'NAME' => $category->getPathName() ] )
			);
			
		};
		
		if($category->isEditable()) {
			Application_Service_Admin::ProductFilter()->init( $category->getAutoAppendProductsFilter() );
			
			
			$GET = Http_Request::GET();
			if(($action = $GET->getString('action'))) {
				
				switch($action) {
					case 'enable_auto_append_mode':
						$category->setAutoAppendProducts( true );
						$category->save();
						$_updated();
						break;
					case 'disable_auto_append_mode':
						$category->setAutoAppendProducts( false );
						$category->save();
						$_updated();
						break;
					case 'remove_all_products':
						if($category->removeAllProducts()) {
							$category->actualizeCategoryBranchProductAssoc();
							$_updated();
						}
						break;
					case 'sort_products':
						$products = explode(',',$GET->getString('products'));
						$category->sortProducts( $products );
						$category->save();
						Application::end();
						
						break;
				}
				
				Http_Headers::reload(unset_GET_params: ['action']);
			}
			
			if(($add_product_id=$GET->getInt('add_product'))) {
				if($category->addProduct( $add_product_id )) {
					$category->actualizeCategoryBranchProductAssoc();
					$_updated();
				}
				Http_Headers::reload(unset_GET_params: ['add_product']);
			}
			
			if(($add_product_id=$GET->getInt('remove_product'))) {
				if($category->removeProduct( $add_product_id )) {
					$category->actualizeCategoryBranchProductAssoc();
					$_updated();
				}
				Http_Headers::reload(unset_GET_params: ['remove_product']);
			}
			
			if($category->getAutoAppendProducts()) {
				if( Application_Service_Admin::ProductFilter()->handleFilterForm() ) {
					if($category->actualizeAutoAppend()) {
						$category->actualizeCategoryBranchProductAssoc();
						
						$_updated();
					}
					Http_Headers::reload();
				}
			} else {
				if( Application_Service_Admin::ProductFilter()->handleFilterForm() ) {
					
					$products = Application_Service_Admin::ProductFilter()->getFilter()->filter();
					foreach($products as $product_id) {
						$category->addProduct( $product_id );
					}
					
					$category->actualizeCategoryBranchProductAssoc();
					$_updated();
					Http_Headers::reload();
				}
				
			}
		}
		
		$this->output( 'edit/products' );
	}
	
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->_setBreadcrumbNavigation( Tr::_( 'Images' ) );
		
		$category = $this->getCurrentItem();
		$category->handleImages();
		
		$this->output( 'edit/images' );
	}
	
	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'New category' ) );
		$new_category = new Category();
		
		if($this->getCurrentCategoryId()) {
			$new_category->setParentId(
				parent_id: $this->getCurrentCategoryId(),
				update_priority: true,
				save: false
			);
		}
		
		$this->_setBreadcrumbNavigation( Tr::_( '<b>Create new category</b>' ) );
		
		
		if($new_category->catchAddForm()) {
			$new_category->save();
			
			UI_messages::success(
				Tr::_( 'Category <b>%NAME%</b> has been created', [ 'NAME' => $new_category->getPathName() ] )
			);
			
			Http_Headers::movedTemporary( $new_category->getEditUrl() );
		}
		
		$this->view->setVar( 'new_category', $new_category );
		
		$this->view->setVar('tabs', '');
		$this->view->setVar('toolbar', 'add/toolbar');
		
		$this->output( 'add' );
		
	}
	
	public function save_sort_Action() : void
	{
		$POST = Http_Request::POST();
		
		$categories = explode('|', $POST->getString('categories_sort_order'));
		
		$priority = 0;
		foreach( $categories as $id ) {
			$category = Category::get( (int)$id );
			if(!$category) {
				continue;
			}
			$priority++;
			
			$category->setPriority( $priority );
		}
		
		Http_Headers::reload([], ['action']);
	}
	
	public function handle_provided_tab_Action() : void
	{
		$this->setBreadcrumbNavigation( $this->selected_provided_tab->getTabTitle() );
		
		$this->content->output(
			$this->view->render('lj_start')
			.$this->selected_provided_tab->handle()
			.$this->view->render('lj_end')
		);
		
	}
	
}