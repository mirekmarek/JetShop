<?php
namespace JetApplicationModule\Admin\Catalog\Categories;

use Jet\Logger;
use Jet\MVC_Controller_Router;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;

use Jet\MVC_Controller_Default;

use Jet\UI;
use Jet\UI_messages;
use Jet\UI_tabs;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Data_Tree_Node;


/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{
	
	protected ?MVC_Controller_Router $router = null;

	protected static ?Category $current_category = null;


	protected static string $current_action = '';


	public function getControllerRouter() : MVC_Controller_Router
	{

		if( !$this->router ) {
			$this->router = new MVC_Controller_Router( $this );

			$GET = Http_Request::GET();

			$category_id = $GET->getInt('id');
			if($category_id) {
				static::$current_category = Category::get($category_id);
				static::$current_category?->setEditable( Main::getCurrentUserCanEdit() );
			}

			$tabs = $this->_getEditTabs();
			if($tabs) {
				$selected_tab = $tabs->getSelectedTabId();

				$this->view->setVar('tabs', $tabs);
			} else {
				$selected_tab = '';
			}


			$action = $GET->getString('action');

			$this->router->setDefaultAction(  'default', Main::ACTION_GET  );

			
			$this->router->addAction( 'add', Main::ACTION_ADD )->setResolver(function() use ($action) {
					return ( $action=='create' );
				});
			
			$this->router->addAction( 'save_sort', Main::ACTION_GET )->setResolver(function() use ($action, $selected_tab) {
				return $action=='save_sort';
			});
			
			$this->router->addAction( 'edit', Main::ACTION_GET )
				->setResolver(function() use ($action, $selected_tab) {
					return static::$current_category && $selected_tab=='main';
				})
				->setURICreator(function( int $id ) : string {
					return Http_Request::currentURI(['id'=>$id],['action']);
				} );
			
			$this->router->addAction( 'edit_images', Main::ACTION_GET )->setResolver(function() use ($action, $selected_tab) {
				return static::$current_category && $selected_tab=='images';
			});
			
			$this->router->addAction( 'edit_products', Main::ACTION_GET )->setResolver(function() use ($action, $selected_tab) {
				return static::$current_category && $selected_tab=='products';
			});
			
		}

		return $this->router;
	}







	public static function getCurrentCategory() : Category|null
	{
		return self::$current_category;
	}


	public static function getCurrentCategoryId() : int
	{
		if(!self::$current_category) {
			return 0;
		}

		return self::$current_category->getId();
	}
	
	/**
	 * @deprecated
	 * @return Data_Tree_Node|null
	 */
	public static function getCurrentNode() : ?Data_Tree_Node
	{
		return Category::getTree()->getNode( Controller_Main::getCurrentCategoryId()?:'' );
	}

	public static function getCurrentAction() : string
	{
		return static::$current_action;
	}


	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();
		
		if(!static::getCurrentCategoryId()) {
			return;
		}
		
		$tree = Category::getTree();

		$current_node = $tree->getNode( static::getCurrentCategoryId() );

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

	protected function _getEditTabs() : UI_tabs|null
	{
		if(!static::$current_category) {
			return null;
		}
		$tabs = [
			'main'     => Tr::_( 'Main data' ),
			'images'   => Tr::_( 'Images' ),
			'products' => Tr::_( 'Products (%count%)', ['count'=>count(static::$current_category->getProductIds())] ),
		];
		

		$tabs = UI::tabs(
			$tabs,
			function($page_id) {
				return Http_Request::currentURI(['page'=>$page_id]);
			},
			Http_Request::GET()->getString('page')
		);

		return $tabs;
	}



	



	public function default_Action() : void
	{
		
		$this->_setBreadcrumbNavigation();

		$this->output( 'default' );
	}
	
	
	public function edit_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit' ) );
		$category = static::getCurrentCategory();
		
		$category->handleActivation();
		
		if( $category->catchEditForm() ) {
			$category->save();
			
			Logger::success(
				event: 'category_updated',
				event_message:  'Category '.$category->getPathName().' ('.$category->getId().') updated',
				context_object_id: $category->getId(),
				context_object_name: $category->getPathName(),
				context_object_data: $category
			);
			
			UI_messages::success(
				Tr::_( 'Category <b>%NAME%</b> has been updated', [ 'NAME' => $category->getPathName() ] )
			);
			
			Http_Headers::reload();
			
		}
		
		$this->view->setVar('toolbar', 'category/edit/main/toolbar');
		
		
		$this->output( 'category/edit/main' );
	}
	
	public function edit_products_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Products' ) );
		$category = static::getCurrentCategory();
		
		$_updated = function() use ($category) {
			Logger::success(
				event: 'category_updated',
				event_message:  'Category '.$category->getPathName().' ('.$category->getId().') updated',
				context_object_id: $category->getId(),
				context_object_name: $category->getPathName(),
				context_object_data: $category
			);
			
			UI_messages::success(
				Tr::_( 'Category <b>%NAME%</b> has been updated', [ 'NAME' => $category->getPathName() ] )
			);
			
		};
		
		$GET = Http_Request::GET();
		if($GET->exists('action')) {
			$action = $GET->getString('action');
			if($action=='change_kind_of_product') {
				$category->setKindOfProductId( Http_Request::POST()->getInt('kind_of_product_id') );
				$_updated();
			}
			
			Http_Headers::reload(unset_GET_params: ['action']);
		}
		
		if(($add_product_id=$GET->getInt('add_product'))) {
			$category->addProduct( $add_product_id );
			$_updated();
			Http_Headers::reload(unset_GET_params: ['add_product']);
		}
		
		if(($add_product_id=$GET->getInt('remove_product'))) {
			$category->removeProduct( $add_product_id );
			$_updated();
			Http_Headers::reload(unset_GET_params: ['remove_product']);
		}
		
		$this->output( 'category/edit/products' );
	}
	
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->_setBreadcrumbNavigation( Tr::_( 'Images' ) );
		
		$category = static::getCurrentCategory();
		$category->handleImages();
		
		$this->output( 'category/edit/images' );
	}
	
	protected function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'New category' ) );
		$new_category = new Category();
		$new_category->setParentId( parent_id: static::getCurrentCategoryId(), update_priority: true, save: false );
		
		$this->_setBreadcrumbNavigation( Tr::_( '<b>Create new category</b>' ) );
		
		
		if($new_category->catchAddForm()) {
			$new_category->save();
			
			Logger::success(
				event: 'category_created',
				event_message: 'Category '.$new_category->getPathName().' ('.$new_category->getId().') created',
				context_object_id: $new_category->getId(),
				context_object_name: $new_category->getPathName(),
				context_object_data: $new_category
			);
			
			UI_messages::success(
				Tr::_( 'Category <b>%NAME%</b> has been created', [ 'NAME' => $new_category->getPathName() ] )
			);
			
			Http_Headers::movedTemporary( $new_category->getEditURL() );
		}
		
		$this->view->setVar( 'new_category', $new_category );
		
		$this->view->setVar('tabs', '');
		$this->view->setVar('toolbar', 'category/add/toolbar');
		
		$this->output( 'category/add' );
		
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
			Logger::success(
				event: 'category_priority_updated',
				event_message: 'Category '.$category->getPathName().' ('.$category->getId().') priority updated',
				context_object_id: $category->getId(),
				context_object_name: $category->getPathName(),
				context_object_data: $priority
			);
		}
		
		Http_Headers::reload([], ['action']);
	}
	
}