<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Admin_EntityManager_Marketing_Interface;
use JetApplication\Admin_Managers_Entity_Edit_Marketing;
use JetApplication\Entity_Marketing;
use JetApplication\Admin_Entity_Marketing_Interface;
use JetApplication\EShops;
use JetApplication\Admin_Managers_Entity_Edit;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;

/**
 *
 */
abstract class Core_Admin_EntityManager_Marketing_Controller extends Admin_EntityManager_Controller
{
	
	/**
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 * @var Application_Module|null|Admin_EntityManager_Marketing_Interface
	 */
	protected ?Application_Module $module = null;
	
	/**
	 * @var Entity_Marketing|Admin_Entity_Marketing_Interface|null
	 */
	protected mixed $current_item = null;
	
	public function newItemFactory(): mixed
	{
		$new_item =  parent::newItemFactory();
		$new_item->setEShop( EShops::getCurrent() );
		
		return $new_item;
	}
	
	
	protected function createEntityEditorModule() : Application_Module|Admin_Managers_Entity_Edit
	{
		return Admin_Managers::EntityEdit_Marketing();
	}
	
	public function getTabs(): array
	{
		$tabs = parent::getTabs();
		
		/**
		 * @var Entity_Marketing $item
		 */
		$item = $this->current_item;
		
		$edit_manager_module_name = Admin_Managers::EntityEdit_Marketing()->getModuleManifest()->getName();
		
		switch( $item->getRelevanceMode() ) {
			case $item::RELEVANCE_MODE_ALL:
				break;
			case $item::RELEVANCE_MODE_BY_FILTER:
				$tabs['filter'] = Tr::_('Filter', dictionary: $edit_manager_module_name );
				break;
			case $item::RELEVANCE_MODE_ALL_BUT_FILTER:
				$tabs['filter'] = Tr::_('Filter - exclude products', dictionary: $edit_manager_module_name );
				break;
			case $item::RELEVANCE_MODE_ONLY_PRODUCTS:
				$tabs['products'] = Tr::_('Products', dictionary: $edit_manager_module_name );
				break;
			case $item::RELEVANCE_MODE_ALL_BUT_PRODUCTS:
				$tabs['products'] = Tr::_('Exclude products', dictionary: $edit_manager_module_name );
				break;
		}
		
		if($item->hasImages()) {
			$tabs['images'] = Tr::_( 'Images' );
		}
		
		return $tabs;
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_filter', $this->module::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='filter';
			} );
		
		$this->router->addAction('edit_products', $this->module::ACTION_UPDATE)
			->setResolver( function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='products';
			} );
		
		$this->router->addAction('edit_images', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='images' && $action=='';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], ['action'] );
			});
		
	}
	
	public function edit_products_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Products') );
		
		$item = $this->current_item;
		
		
		
		/**
		 * @var Admin_Entity_Marketing_Interface $item
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
		 * @var Admin_Entity_Marketing_Interface $item
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
	
	
	public function getEditorManager(): Admin_Managers_Entity_Edit_Marketing|Application_Module
	{
		if(!$this->editor_manager) {
			$this->editor_manager = Admin_Managers::EntityEdit_Marketing();
			$this->editor_manager->init(
				$this->current_item,
				$this->getListing(),
				$this->tabs
			);
		}
		
		return $this->editor_manager;
	}
	
	
}