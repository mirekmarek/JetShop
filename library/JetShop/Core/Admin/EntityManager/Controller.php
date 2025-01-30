<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\Form;
use Jet\Locale;
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
use JetApplication\Application_Admin;
use JetApplication\Entity_Admin_WithEShopData_Interface;
use JetApplication\Entity_Basic;
use JetApplication\Entity_HasEShopRelation_Interface;
use JetApplication\Entity_HasImages_Interface;
use JetApplication\Entity_HasProductsRelation_Interface;
use JetApplication\Entity_WithEShopData;
use JetApplication\EShop;
use JetApplication\EShops;
use Closure;

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
	
	protected ?Admin_Managers_Entity_Listing $listing_manager = null;
	
	protected ?UI_tabs $tabs = null;
	
	protected ?Admin_Managers_Entity_Edit $editor_manager = null;
	
	/**
	 * @var Entity_Basic|null
	 */
	protected mixed $current_item = null;
	
	
	abstract public function getEntityNameReadable() : string;
	
	protected function generateText_add_msg() : string
	{
		return Tr::_( $this->getEntityNameReadable().' <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $this->current_item->getAdminTitle() ] );
	}
	
	protected function generateText_edit_main_msg() : string
	{
		return Tr::_( $this->getEntityNameReadable().' <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $this->current_item->getAdminTitle() ] );
	}
	
	protected function generateText_delete_msg() : string
	{
		return Tr::_( $this->getEntityNameReadable().' <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $this->current_item->getAdminTitle() ] );
	}
	
	protected function generateText_delete_confirm_msg() : string
	{
		return Tr::_( 'Do you really want to delete this '.$this->getEntityNameReadable().'?' );
	}
	
	protected function generateText_delete_not_possible_msg() : string
	{
		return Tr::_( 'It is not possible to delete this '.$this->getEntityNameReadable() );
	}
	
	protected function getTabs() : array
	{
		$item = $this->current_item;
		
		$tabs = [];
		$tabs['main'] = Tr::_( 'Main data' );
		
		if(
			$item instanceof Entity_Admin_WithEShopData_Interface &&
			$item->getSeparateTabFormShopData()
		) {
			$tabs['description'] = Tr::_('Description');
		}
		
		if($item instanceof Entity_HasProductsRelation_Interface) {
			switch( $this->current_item->getRelevanceMode() ) {
				case $item::RELEVANCE_MODE_ALL:
					break;
				case $item::RELEVANCE_MODE_BY_FILTER:
					$tabs['filter'] = Tr::_('Filter' );
					break;
				case $item::RELEVANCE_MODE_ALL_BUT_FILTER:
					$tabs['filter'] = Tr::_('Filter - exclude products' );
					break;
				case $item::RELEVANCE_MODE_ONLY_PRODUCTS:
					$tabs['products'] = Tr::_('Products' );
					break;
				case $item::RELEVANCE_MODE_ALL_BUT_PRODUCTS:
					$tabs['products'] = Tr::_('Exclude products' );
					break;
			}
			
		}
		
		if(
			$item instanceof Entity_HasImages_Interface
		) {
			$tabs['images'] = Tr::_( 'Images' );
		}
		
		return $tabs;
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
		
		if(
			$this->current_item instanceof Entity_Admin_WithEShopData_Interface &&
			$this->current_item->getSeparateTabFormShopData()
		) {
			$this->router->addAction('edit_description', $this->module::ACTION_UPDATE)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->current_item && $selected_tab=='description' && $action=='';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'description'], ['action'] );
				});
		}
		
		
		if($this->current_item instanceof Entity_HasImages_Interface)
		{
			$this->router->addAction('edit_images', $this->module::ACTION_UPDATE)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->current_item && $selected_tab=='images' && $action=='';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], ['action'] );
				});
		}
		
		
		if($this->current_item instanceof Entity_HasProductsRelation_Interface) {
			$this->router->addAction('edit_filter', $this->module::ACTION_UPDATE)
				->setResolver( function() use ($action, $selected_tab) {
					return $this->current_item && $selected_tab=='filter';
				} );
			
			$this->router->addAction('edit_products', $this->module::ACTION_UPDATE)
				->setResolver( function() use ($action, $selected_tab) {
					return $this->current_item && $selected_tab=='products';
				} );
		}
		
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
		$new_item =  $this->module::getEntityInstance();
		
		if($new_item instanceof Entity_HasEShopRelation_Interface) {
			$new_item->setEShop( EShops::getCurrent() );
		}
		
		return $new_item;
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

	
	protected function getDescriptionMode(): bool
	{
		if(!$this->current_item instanceof Entity_WithEShopData) {
			return false;
		}
		
		return $this->current_item->getDescriptionMode();
	}
	
	
	protected function getAddCommonFieldsRenderer() : ?Closure
	{
		return function() {
			echo $this->view->render('add/common-form-fields');
		};
	}
	
	protected function getAddEshopDataFieldsRenderer() : ?Closure
	{
		if($this->getDescriptionMode()) {
			return null;
		}
		
		return function( EShop $eshop ) {
			$this->view->setVar('eshop', $eshop);
			echo $this->view->render('add/shop-data-form-fields');
		};
	}
	
	protected function getAddDescriptionFieldsRenderer() : ?Closure
	{
		if(!$this->getDescriptionMode()) {
			return null;
		}
		return function( Locale $locale, string $locale_str ) {
			$this->view->setVar('locale', $locale);
			$this->view->setVar('locale_str', $locale_str);
			echo $this->view->render('add/description-form-fields');
		};
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
			$this->getEditorManager()->renderAdd( $form )
		);
		
	}
	
	protected function getEditToolbarRenderer() : ?Closure
	{
		return function( Entity_Basic $item, ?Form $form=null ) {
			$this->view->setVar('item', $item);
			$this->view->setVar('form', $form);
			
			echo $this->view->render('edit/toolbar');
		};
	}
	
	protected function getEditCommonDataFieldsRenderer() : ?Closure
	{
		return function( Entity_Basic $item, Form $form ) {
			$this->view->setVar('item', $item);
			$this->view->setVar('form', $form);
			
			echo $this->view->render('edit/main/common-form-fields');
		};
	}
	
	protected function getEditEshopDataFieldsRenderer() : ?Closure
	{
		return function( EShop $eshop, string $eshop_key, Entity_Basic $item, Form $form  ) {
			$this->view->setVar('eshop', $eshop);
			$this->view->setVar('item', $item);
			$this->view->setVar('form', $form);
			echo $this->view->render('edit/main/shop-data-form-fields');
		};
	}
	
	protected function getEditDescriptionFieldsRenderer() : ?Closure
	{
		return function( Locale $locale, string $locale_str, Entity_Basic $item, Form $form ) {
			
			$this->view->setVar('locale', $locale);
			$this->view->setVar('locale_str', $locale_str);
			$this->view->setVar('item', $item);
			$this->view->setVar('form', $form);
			echo $this->view->render('edit/main/description-form-fields');
		};
	}
	
	public function edit_description_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Description') );
		
		$item = $this->current_item;
		
		$this->view->setVar('item', $item);
		
		$form = $item->getDescriptionEditForm();
		
		if( $item->catchDescriptionEditForm() ) {
			
			UI_messages::success(
				Tr::_( $this->getEntityNameReadable().' description <b>%NAME%</b> has been updated', [ 'NAME' => $item->getAdminTitle() ] )
			);
			
			Http_Headers::reload();
		}
		
		$this->content->output(
			$this->getEditorManager()->renderEditDescription( $form )
		);
		

	}
	
	public function edit_main_Action() : void
	{
		$this->setBreadcrumbNavigation();
		
		$item = $this->current_item;
		
		$this->edit_main_handleActivation();
		
		$form = $item->getEditMainForm();
		
		if( $item->catchEditMainForm() ) {
			
			$item->save();
			
			UI_messages::success(
				$this->generateText_edit_main_msg()
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $item );
		
		$this->content->output(
			$this->getEditorManager()->renderEditMain( $form )
		);
		
	}
	
	
	public function edit_main_handleActivation( ?Entity_WithEShopData $item=null ) : void
	{
		$item = $item?:$this->current_item;
		$entity_type = $item->getEntityType();
		$entity_id = $item->getId();
		
		
		$GET = Http_Request::GET();
		
		if($GET->exists('deactivate_entity')) {
			$item->deactivate();
			Http_Headers::reload(unset_GET_params: ['deactivate_entity']);
		}
		
		if($GET->exists('activate_entity')) {
			$item->activate();
			Http_Headers::reload(unset_GET_params: ['activate_entity']);
		}
		
		
		if($GET->exists('activate_entity_completely')) {
			$item->activateCompletely();
			Http_Headers::reload(unset_GET_params: ['activate_entity_completely']);
		}
		
		
		if($GET->exists('activate_entity_eshop_data')) {
			$eshop = EShops::get( $GET->getString('activate_entity_eshop_data') );
			$item->activateEShopData( $eshop );
			
			Http_Headers::reload(unset_GET_params: ['activate_entity_eshop_data']);
		}
		
		if($GET->exists('deactivate_entity_eshop_data')) {
			$eshop = EShops::get( $GET->getString('deactivate_entity_eshop_data') );
			$item->deactivateEShopData( $eshop );
			
			Http_Headers::reload(unset_GET_params: ['deactivate_entity_eshop_data']);
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
			$this->getEditorManager()->renderEditImages()
		);
	}
	
	
	public function edit_products_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Products') );
		
		$item = $this->current_item;
		
		
		
		/**
		 * @var Entity_HasProductsRelation_Interface $item
		 */
		if($item->isEditable()) {
			
			$GET = Http_Request::GET();
			if( $GET->getString('action')=='remove_all_products' ) {
				if($item->removeAllProducts()) {
					UI_messages::success(
						$this->generateText_edit_main_msg()
					);
				}
				Http_Headers::reload(unset_GET_params: ['action']);
			}
			if(($add_product_id=$GET->getInt('add_product'))) {
				if($item->addProduct( $add_product_id )) {
					UI_messages::success(
						$this->generateText_edit_main_msg()
					);
				}
				Http_Headers::reload(unset_GET_params: ['add_product']);
			}
			
			if(($add_product_id=$GET->getInt('remove_product'))) {
				if($item->removeProduct( $add_product_id )) {
					UI_messages::success(
						$this->generateText_edit_main_msg()
					);
				}
				Http_Headers::reload(unset_GET_params: ['remove_product']);
			}
		}
		
		$this->content->output(
			$this->getEditorManager()->renderEditProducts( $item )
		);
	}
	
	public function edit_filter_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Filter') );
		
		
		$item = $this->current_item;
		
		$this->view->setVar('item', $this->current_item);
		
		
		
		
		$form = Admin_Managers::ProductFilter()->init(
			$this->current_item->getProductsFilter()
		);
		
		if(!$this->current_item->isEditable()) {
			$form->setIsReadonly();
		}
		
		
		/**
		 * @var Entity_HasProductsRelation_Interface $item
		 */
		if($item->isEditable()) {
			if(Admin_Managers::ProductFilter()->handleFilterForm()) {
				
				UI_messages::success(
					$this->generateText_edit_main_msg()
				);
				Http_Headers::reload();
				
			}
		}
		
		
		$this->content->output(
			$this->getEditorManager()->renderEditFilter( $item, $form )
		);
	}
	
	
	
	public function getEditorManager(): Admin_Managers_Entity_Edit|Application_Module
	{
		if(!$this->editor_manager) {
			$this->editor_manager = Admin_Managers::EntityEdit();
			$this->editor_manager->init(
				item: $this->current_item,
				listing: $this->getListing(),
				tabs: $this->tabs,
				common_data_fields_renderer: $this->getEditCommonDataFieldsRenderer(),
				toolbar_renderer: $this->getEditToolbarRenderer(),
				eshop_data_fields_renderer: $this->getEditEshopDataFieldsRenderer(),
				description_fields_renderer: $this->getEditDescriptionFieldsRenderer()
			);
		}
		
		return $this->editor_manager;
	}
	
	
}