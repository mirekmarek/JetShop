<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\UI;

use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Default;
use Jet\UI_messages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\UI_tabs;

use JetApplication\Admin_EntityManager_Interface;
use JetApplication\Admin_Managers_Entity_Listing;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Admin_Managers;

/**
 *
 */
abstract class Core_Admin_EntityManager_Controller extends MVC_Controller_Default
{
	
	/**
	 * @var Application_Module|null|Admin_EntityManager_Interface
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 */
	protected ?Application_Module $module = null;
	
	protected ?MVC_Controller_Router $router = null;
	
	protected mixed $current_item = null;
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;
	
	protected ?UI_tabs $tabs = null;
	
	protected ?Admin_Managers_Entity_Edit $editor_manager = null;
	
	protected function getEntityNameReadable() : string
	{
		return $this->module::getEntityNameReadable();
	}
	
	protected function generateText_add_msg() : string
	{
		return Tr::_( ucfirst($this->getEntityNameReadable()).' <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $this->current_item->getAdminTitle() ] );
	}
	
	protected function generateText_edit_main_msg() : string
	{
		return Tr::_( ucfirst($this->getEntityNameReadable()).' <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $this->current_item->getAdminTitle() ] );
	}
	
	protected function generateText_delete_msg() : string
	{
		return Tr::_( ucfirst($this->getEntityNameReadable()).' <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $this->current_item->getAdminTitle() ] );
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
			//'images' => Tr::_( 'Images' ),
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
	
	protected function newItemFactory() : mixed
	{
		return $this->module::getEntityInstance();
	}
	
	public function add_Action() : void
	{
		$this->current_item = $this->newItemFactory();
		
		$this->setBreadcrumbNavigation( Tr::_('Create new') );
		
		
		$form = $this->current_item->getAddForm();
		
		if( $this->current_item->catchAddForm() ) {
			$this->current_item->save();
			
			
			UI_messages::success( $this->generateText_add_msg() );
			
			Http_Headers::reload(
				set_GET_params: ['id'=>$this->current_item->getId()],
				unset_GET_params: ['action']
			);
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $this->current_item );
		
		$this->content->output(
			$this->getEditorManager()->renderAdd(
				common_data_fields_renderer: function() {
					echo $this->view->render('add/common-form-fields');
				}
			)
		);
		
	}
	
	public function edit_main_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$item = $this->current_item;
		
		$this->edit_main_handleActivation();
		
		$form = $item->getEditForm();
		
		if( $item->catchEditForm() ) {
			
			$item->save();
			
			UI_messages::success(
				$this->generateText_edit_main_msg()
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $item );
		
		
		$this->content->output(
			$this->getEditorManager()->renderEditMain(
				common_data_fields_renderer: function() {
					echo $this->view->render('edit/main/common-form-fields');
				},
				toolbar_renderer: function() {
					echo $this->view->render('edit/toolbar');
				}
			)
		);
		
	}
	
	public function edit_main_handleActivation() : void
	{
	}
	
	
	public function delete_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Delete') );
		
		$item = $this->current_item;
		
		$this->view->setVar( 'item', $item );
		
		if($item->isItPossibleToDelete()) {
			if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
				$item->delete();
				
				
				UI_messages::info(
					$this->generateText_delete_msg()
				);
				
				Http_Headers::reload([], ['action', 'id']);
			}
			
			$this->content->output(
				$this->getEditorManager()->renderDeleteConfirm(
					message: $this->generateText_delete_confirm_msg()
				)
			);
			
		} else {
			$this->content->output(
				$this->getEditorManager()->renderDeleteNotPossible(
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
		$title = '';
		
		if(
			$this->current_item &&
			!$this->current_item->getIsNew()
		) {
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

	
	abstract public function getEditorManager(): Application_Module|Admin_Managers_Entity_Edit;
	
}