<?php
namespace JetShopModule\Admin\Catalog\Categories;


use Jet\AJAX;
use Jet\Logger;
use Jet\MVC_Controller_Router;
use JetShop\Application_Admin;
use JetShop\Category;
use JetShop\Fulltext_Index_Internal_Category;

use Jet\MVC_Controller_Default;

use Jet\UI;
use Jet\UI_messages;
use Jet\UI_tabs;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Data_Tree_Node;

use JetShop\Shops;
use Jet\Application;
use JetShopModule\Admin\UI\Main as UI_module;


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
			}

			$tabs = $this->_getEditTabs();
			if($tabs) {
				$selected_tab = $tabs->getSelectedTabId();

				$this->view->setVar('tabs', $tabs);
			} else {
				$selected_tab = '';
			}


			$action = $GET->getString('action');

			$this->router->setDefaultAction(  'default', Main::ACTION_GET_CATEGORY  );

			$this->router->addAction( 'whisper' )->setResolver(function() use ($GET) {
				return $GET->exists('whisper');
			});

			$this->router->addAction( 'set_filter' )->setResolver(function() use ($GET) {
				return $GET->exists('set_filter');
			});

			$this->router->addAction( 'generate_url_path_part' )->setResolver(function() use ($GET) {
				return $GET->exists('generate_url_path_part');
			});
			
			$this->router->addAction( 'category_add', Main::ACTION_ADD_CATEGORY )
				->setResolver(function() use ($action) {
					return ( $action=='create_category' );
				});
			
			$this->router->addAction( 'save_sort', Main::ACTION_GET_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
				return $action=='save_sort';
			});
			
			if(static::$current_category) {
				$this->router->addAction( 'category_edit', Main::ACTION_GET_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
					return $selected_tab=='main';
				});
				
				$this->router->addAction( 'category_edit_filter', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
					return $selected_tab=='filter';
				});
				
				$this->router->addAction( 'category_edit_images', Main::ACTION_UPDATE_CATEGORY )->setResolver(function() use ($action, $selected_tab) {
					return $selected_tab=='images';
				});
				
			}
			
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

	public static function getCurrentNode() : Data_Tree_Node
	{
		return Category::getTree()->getNode( Controller_Main::getCurrentCategoryId() );
	}

	public static function getCurrentAction() : string
	{
		return static::$current_action;
	}


	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		UI_module::initBreadcrumb();


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

			Navigation_Breadcrumb::addURL( $label, Category::getCategoryEditURL( $node->getId() ) );

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
			'main'    => Tr::_( 'Main data' ),
			'images'  => Tr::_( 'Images' ),
		];
		
		if(static::$current_category->getKindOfProductId()) {
			$tabs['filter'] = Tr::_( 'Auto append products' );
		}

		$tabs = UI::tabs(
			$tabs,
			function($page_id) {
				return Http_Request::currentURI(['page'=>$page_id]);
			},
			Http_Request::GET()->getString('page')
		);

		return $tabs;
	}




	protected function _editCategorySave( bool $reload = true ) : void
	{
		$category = static::getCurrentCategory();
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

		if($reload) {
			Http_Headers::reload();
		}

	}




	public function default_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$this->output( 'default' );
	}

	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();

		$only_types = $GET->getString('only_types');
		if($only_types) {
			$only_types = explode(',', $only_types);
		} else {
			$only_types = [];
		}

		$result = Fulltext_Index_Internal_Category::search(
			$GET->getString('whisper'),
			$only_types,
			$GET->getBool('only_active'),
			$GET->getInt('exclude_branch_id')
		);

		$this->view->setVar('result', $result);
		echo $this->view->render('search_whisperer_result');

		Application::end();
	}

	public function set_filter_Action() : void
	{
		$POST = Http_Request::POST();

		Category::setFilter_onlyActive( $POST->getBool('only_active') );
		Category::setFilter_selectedSort( $POST->getString('order_by') );

		Http_Headers::reload([], ['set_filter']);
	}


	public function generate_url_path_part_Action() : void
	{
		$GET = Http_Request::GET();

		AJAX::commonResponse([
			'url_path_part' => Shops::generateURLPathPart( $GET->getString('generate_url_path_part'), '', 0, Shops::get( $GET->getString('shop_key') ) )
		]);

		Application::end();
	}
	
	
	
	public function category_edit_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit' ) );
		$category = static::getCurrentCategory();
		
		$GET = Http_Request::GET();
		if($GET->exists('action')) {
			$action = $GET->getString('action');
			if($action=='change_kind_of_product') {
				$category->setKindOfProductId( Http_Request::POST()->getInt('kind_of_product_id') );
				
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
			}
			
			Http_Headers::reload(unset_GET_params: ['action']);
		}
		
		
		if( $category->catchEditForm() ) {
			$this->_editCategorySave();
		}
		
		$this->view->setVar('toolbar', 'category/edit/main/toolbar');
		
		
		$this->output( 'category/edit/main' );
	}
	
	public function category_edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$this->_setBreadcrumbNavigation( Tr::_( 'Images' ) );
		
		$category = static::getCurrentCategory();
		
		foreach(Shops::getList() as $shop) {
			$category->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'Category image',
				object_id: $category->getId(),
				object_name: $category->getPathName(),
				upload_event: 'category_image_uploaded',
				delete_event: 'category_image_deleted'
			);
		}
		
		$this->output( 'category/edit/images' );
	}
	
	
	
	public function category_edit_filter_Action()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit - filter settings' ) );
		$category = static::getCurrentCategory();
		
		if( $category->catchAutoAppendProductFilterEditForm() ) {
			$this->_editCategorySave();
		}
		
		$this->output( 'category/edit/auto-append-settings' );
		
	}
	
	
	
	protected function category_add()
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'New category' ) );
		$new_category = new Category();
		$new_category->setParentId( static::getCurrentCategoryId() );
		
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
			$category->save();
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