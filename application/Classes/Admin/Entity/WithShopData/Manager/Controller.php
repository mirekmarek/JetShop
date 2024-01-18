<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplication;

use Jet\Application_Module;
use Jet\MVC;
use Jet\UI;

use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Default;
use Jet\UI_messages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Logger;
use Jet\UI_tabs;


abstract class Admin_Entity_WithShopData_Manager_Controller extends MVC_Controller_Default
{
	
	/**
	 * @var Application_Module|null|Admin_Managers_PropertyGroup
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 */
	protected ?Application_Module $module = null;
	
	protected ?MVC_Controller_Router $router = null;
	
	protected null|Entity_WithShopData|Admin_Entity_WithShopData_Interface $current_item = null;
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;
	
	protected UI_tabs $tabs;
	
	protected function getEntityNameReadable() : string
	{
		return $this->module::getEntityNameReadable();
	}
	
	protected function generateText_add_msg() : string
	{
		return Tr::_( ucfirst($this->getEntityNameReadable()).' <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $this->current_item->getInternalName() ] );
	}
	
	protected function generateText_edit_main_msg() : string
	{
		return Tr::_( ucfirst($this->getEntityNameReadable()).' <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $this->current_item->getInternalName() ] );
	}
	
	protected function generateText_delete_msg() : string
	{
		return Tr::_( ucfirst($this->getEntityNameReadable()).' <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $this->current_item->getInternalName() ] );
	}
	
	protected function generateText_delete_confirm_msg() : string
	{
		return Tr::_( 'Do you really want to delete this '.ucfirst($this->getEntityNameReadable()).'?' );
	}
	
	protected function generateText_delete_not_possible_msg() : string
	{
		return Tr::_( 'It is not possible to delete this '.ucfirst($this->getEntityNameReadable()) );
	}
	
	
	protected function getTabs() : array
	{
		return [
			'main'   => Tr::_( 'Main data' ),
			'images' => Tr::_( 'Images' ),
		];
	}
	
	protected function currentItemGetter() : void
	{
		$current_id = Http_Request::GET()->getInt( 'id' );
		$this->current_item = $this->module::getEntityInstance()::get( $current_id );
		if($this->current_item) {
			$this->current_item->setEditable($this->module::getCurrentUserCanEdit());
		}
	}
	
	protected function initTabs() : string
	{
		$_tabs = $this->getTabs();
		
		
		$this->tabs = UI::tabs(
			$_tabs,
			function($page_id) {
				return Http_Request::currentURI(['page'=>$page_id]);
			},
			Http_Request::GET()->getString('page', 'main')
		);
		$this->view->setVar('tabs', $this->tabs);
		
		return $this->tabs->getSelectedTabId();
	}
	
	public function getControllerRouter() : MVC_Controller_Router
	{
		if( !$this->router ) {
			$action = Http_Request::GET()->getString('action');
			$selected_tab = '';
			
			$this->currentItemGetter();
			
			if($this->current_item) {
				$selected_tab =$this->initTabs();
			}
			
			$this->router = new MVC_Controller_Router( $this );
			
			$this->setupRouter( $action, $selected_tab );
			
		}
		
		return $this->router;
	}
	
	protected function setupRouter( string $action, string $selected_tab ) : void
	{
		$this->router->setDefaultAction('listing', $this->module::ACTION_GET);
		$this->router->getAction('listing')->setResolver(function() use ($action) {
			return (!$this->current_item && !$action) ;
		});
		
		$this->router->addAction('add', $this->module::ACTION_ADD)
			->setResolver(function() use ($action) {
				return ($action=='add' && !$this->current_item);
			})
			->setURICreator(function() {
				return Http_Request::currentURI( ['action' => 'add'], ['id', 'page'] );
			});
		
		$this->router->addAction('delete', $this->module::ACTION_DELETE)
			->setResolver(function() use ($action) {
				return $this->current_item && $action=='delete';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'action'=>'delete'], ['page'] );
			});
		
		
		$this->router->addAction('edit_main', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='main' && $action=='';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id], ['page','action'] );
			});
		
		$this->router->addAction('edit_images', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='images' && $action=='';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], ['action'] );
			});
		
	}
	
	
	public function getListing() : Admin_Managers_Entity_Listing
	{
		if(!$this->listing_manager) {
			$this->listing_manager = Admin_Managers::EntityListing();
			$this->listing_manager->setUp(
				$this->module
			);
			
			$this->setupListing();
		}
		
		return $this->listing_manager;
	}
	
	protected function setupListing() : void
	{
	
	}
	
	public function listing_Action() : void
	{
		$this->setBreadcrumbNavigation();
		$this->content->output( $this->getListing()->renderListing() );
	}
	
	protected function newItemFactory() : Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		return $this->module::getEntityInstance();
	}
	
	public function add_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Create new') );
		
		$item = $this->newItemFactory();
		
		$form = $item->getAddForm();
		
		if( $item->catchAddForm() ) {
			$item->save();
			
			$this->current_item = $item;
			
			Logger::success(
				event: $item::getEntityType().'_created',
				event_message: $item::getEntityType().' created',
				context_object_id: $item->getId(),
				context_object_name: $item->getInternalName(),
				context_object_data: $item
			);
			
			UI_messages::success( $this->generateText_add_msg() );
			
			Http_Headers::reload(
				set_GET_params: ['id'=>$item->getId()],
				unset_GET_params: ['action']
			);
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $item );
		
		$this->content->output(
			Admin_Managers::EntityEdit_WithShopData()->renderAdd(
				item: $item,
				common_data_fields_renderer: function() {
					echo $this->view->render('add/common-form-fields');
				},
				shop_data_fields_renderer: function( Shops_Shop $shop ) {
					$this->view->setVar('shop', $shop);
					echo $this->view->render('add/shop-data-form-fields');
				}
			)
		);
		
		
	}
	
	/**
	 *
	 */
	public function edit_main_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$item = $this->current_item;
		
		$this->edit_main_handleActivation();
		
		$form = $item->getEditForm();
		
		if( $item->catchEditForm() ) {
			
			$item->save();
			
			Logger::success(
				event: $item::getEntityType().'_updated',
				event_message: $item::getEntityType().' updated',
				context_object_id: $item->getId(),
				context_object_name: $item->getInternalName(),
				context_object_data: $item
			);
			
			UI_messages::success(
				$this->generateText_edit_main_msg()
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $item );
		
		$this->content->output(
			Admin_Managers::EntityEdit_WithShopData()->renderEditMain(
				item: $item,
				tabs: $this->tabs,
				listing: $this->getListing(),
				common_data_fields_renderer: function() {
					echo $this->view->render('edit/main/common-form-fields');
				},
				shop_data_fields_renderer: function( Shops_Shop $shop ) {
					$this->view->setVar('shop', $shop);
					
					echo $this->view->render('edit/main/shop-data-form-fields');
				},
				toolbar_renderer: function() {
					echo $this->view->render('edit/toolbar');
				}
			
			)
		);
		
	}
	
	public function edit_main_handleActivation( ?Entity_WithShopData $item=null ) : void
	{
		$item = $item?:$this->current_item;
		$entity_type = $item->getEntityType();
		$entity_id = $item->getId();
		
		$logEntityActivation = function() use ($entity_type, $entity_id, $item) {
			
			Logger::success(
				event: 'entity_activated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$item->getInternalName().'\' ('.$entity_id.') activated',
				context_object_id: $entity_id,
				context_object_name: $item->getInternalName()
			);
		};
		$logEntityDeactivation = function() use ($entity_type, $entity_id, $item) {
			Logger::success(
				event: 'entity_deactivated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$item->getInternalName().'\' ('.$entity_id.') deactivated',
				context_object_id: $entity_id,
				context_object_name: $item->getInternalName()
			);
		};
		$logEntityShopDataActivation = function( Shops_Shop $shop ) use ($entity_type, $entity_id, $item) {
			Logger::success(
				event: 'entity_shop_data_activated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$item->getInternalName().'\' ('.$entity_id.') shop data '.$shop->getKey().' activated',
				context_object_id: $entity_id.':'.$shop->getKey(),
				context_object_name: $item->getInternalName().' ('.$shop->getShopName().')'
			);
		};
		$logEntityShopDataDeactivation = function( Shops_Shop $shop ) use ($entity_type, $entity_id, $item) {
			Logger::success(
				event: 'entity_shop_data_deactivated:'.$entity_type,
				event_message: 'Entity '.$entity_type.' \''.$item->getInternalName().'\' ('.$entity_id.') shop data '.$shop->getKey().' deactivated',
				context_object_id: $entity_id.':'.$shop->getKey(),
				context_object_name: $item->getInternalName().' ('.$shop->getShopName().')'
			);
		};
		
		$GET = Http_Request::GET();
		
		if($GET->exists('deactivate_entity')) {
			if($item->isActive()) {
				$item->deactivate();
				$logEntityDeactivation();
			}
			
			Admin_Managers::FulltextSearch()->updateIndex( $item );
			Http_Headers::reload(unset_GET_params: ['deactivate_entity']);
		}
		if($GET->exists('activate_entity')) {
			if(!$item->isActive()) {
				$item->activate();
				$logEntityActivation();
			}
			
			Admin_Managers::FulltextSearch()->updateIndex( $item );
			Http_Headers::reload(unset_GET_params: ['activate_entity']);
		}
		if($GET->exists('activate_entity_completely')) {
			if(!$item->isActive()) {
				$item->activate();
				$logEntityActivation();
			}
			
			foreach(Shops::getList() as $shop) {
				if(!$item->getShopData($shop)->isActiveForShop()) {
					$item->getShopData($shop)->activate();
					$logEntityShopDataActivation( $shop );
				}
			}
			
			Admin_Managers::FulltextSearch()->updateIndex( $item );
			Http_Headers::reload(unset_GET_params: ['activate_entity_completely']);
		}
		if($GET->exists('activate_entity_shop_data')) {
			$shop = Shops::get( $GET->getString('activate_entity_shop_data') );
			
			if(!$item->getShopData($shop)->isActiveForShop()) {
				$item->getShopData($shop)->activate();
				$logEntityShopDataActivation( $shop );
			}
			
			Admin_Managers::FulltextSearch()->updateIndex( $item );
			Http_Headers::reload(unset_GET_params: ['activate_entity_shop_data']);
		}
		if($GET->exists('deactivate_entity_shop_data')) {
			$shop = Shops::get( $GET->getString('deactivate_entity_shop_data') );
			
			if($item->getShopData($shop)->isActiveForShop()) {
				$item->getShopData($shop)->deactivate();
				$logEntityShopDataDeactivation( $shop );
			}
			
			Admin_Managers::FulltextSearch()->updateIndex( $item );
			Http_Headers::reload(unset_GET_params: ['deactivate_entity_shop_data']);
		}
		
	}
	
	
	public function edit_images_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Images') );
		
		Application_Admin::handleUploadTooLarge();
		
		$item = $this->current_item;
		$item->handleImages();
		
		$this->view->setVar( 'item', $item );
		
		$this->content->output(
			Admin_Managers::EntityEdit_WithShopData()->renderEditImages(
				item: $item,
				tabs: $this->tabs,
				listing: $this->getListing()
			)
		);
	}
	
	public function delete_Action() : void
	{
		$item = $this->current_item;
		
		$this->setBreadcrumbNavigation( Tr::_('Delete') );

		$this->view->setVar( 'item', $item );
		
		if($item->isItPossibleToDelete()) {
			if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
				$item->delete();
				
				Logger::success(
					event: $item::getEntityType().'_deleted',
					event_message: $item::getEntityType().' deleted',
					context_object_id: $item->getId(),
					context_object_name: $item->getInternalName(),
					context_object_data: $item
				);
				
				UI_messages::info(
					$this->generateText_delete_msg()
				);
				
				Http_Headers::reload([], ['action', 'id']);
			}
			
			$this->content->output(
				Admin_Managers::EntityEdit_Common()->renderDeleteConfirm(
					item: $item,
					message: $this->generateText_delete_confirm_msg()
				)
			);
			
		} else {
			$this->content->output(
				Admin_Managers::EntityEdit_Common()->renderDeleteNotPossible(
					item: $item,
					message: $this->generateText_delete_not_possible_msg(),
					reason_renderer: function() {
						//TODO:
					}
				)
			);
		}
		
	}
	
	

	protected function setBreadcrumbNavigation( string $current_label = '', string $URL = '' ) : void
	{
		$page = MVC::getPage();
		
		Navigation_Breadcrumb::reset();
		
		Navigation_Breadcrumb::addURL(
			UI::icon( $page->getIcon() ).'&nbsp;&nbsp;'.$page->getBreadcrumbTitle(),
			Http_Request::currentURI(unset_GET_params: [
				'id',
				'action'
			])
		);
		
		$title = '';
		
		if($this->current_item) {
			$title = $this->current_item->getAdminTitle();
			
			if($current_label) {
				$title .= ' - '.$current_label;
			}
		} else {
			$title = $current_label;
		}
		
		if($title) {
			Navigation_Breadcrumb::addURL( $title, $URL );
		}
	}
	
}