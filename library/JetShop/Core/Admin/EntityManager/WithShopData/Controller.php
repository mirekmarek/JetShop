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
use JetApplication\Admin_EntityManager_WithShopData_Interface;
use JetApplication\Admin_Managers_Entity_Edit_WithShopData;
use JetApplication\Entity_WithShopData;
use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Shops_Shop;
use JetApplication\Shops;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;


abstract class Core_Admin_EntityManager_WithShopData_Controller extends Admin_EntityManager_Controller
{
	
	/**
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 * @var Application_Module|null|Admin_EntityManager_WithShopData_Interface
	 */
	protected ?Application_Module $module = null;
	
	/**
	 * @var Entity_WithShopData|Admin_Entity_WithShopData_Interface|null
	 */
	protected mixed $current_item = null;
	
	protected function getTabs() : array
	{
		return [
			'main'   => Tr::_( 'Main data' ),
			'images' => Tr::_( 'Images' ),
		];
	}
	
	protected function setupRouter( string $action, string $selected_tab ) : void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('edit_images', $this->module::ACTION_UPDATE)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && $selected_tab=='images' && $action=='';
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], ['action'] );
			});
		
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
				},
				shop_data_fields_renderer:function( Shops_Shop $shop ) {
					$this->view->setVar('shop', $shop);
					echo $this->view->render('add/shop-data-form-fields');
				},
			
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
				},
				shop_data_fields_renderer:function( Shops_Shop $shop ) {
					$this->view->setVar('shop', $shop);
					echo $this->view->render('edit/main/shop-data-form-fields');
				},
			)
		);
		
	}
	
	
	public function edit_main_handleActivation( ?Entity_WithShopData $item=null ) : void
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
		
		
		if($GET->exists('activate_entity_shop_data')) {
			$shop = Shops::get( $GET->getString('activate_entity_shop_data') );
			$item->activateShopData( $shop );
			
			Http_Headers::reload(unset_GET_params: ['activate_entity_shop_data']);
		}
		
		if($GET->exists('deactivate_entity_shop_data')) {
			$shop = Shops::get( $GET->getString('deactivate_entity_shop_data') );
			$item->deactivateShopData( $shop );
			
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
			$this->getEditorManager()->renderEditImages()
		);
	}
	
	
	public function getEditorManager(): Admin_Managers_Entity_Edit_WithShopData|Application_Module
	{
		if(!$this->editor_manager) {
			$this->editor_manager = Admin_Managers::EntityEdit_WithShopData();
			$this->editor_manager->init(
				$this->current_item,
				$this->getListing(),
				$this->tabs
			);
		}
		
		return $this->editor_manager;
	}
	
}