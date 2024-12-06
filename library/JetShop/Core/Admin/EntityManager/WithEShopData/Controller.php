<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetShop;

use Closure;
use Jet\Application_Module;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Locale;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Admin_EntityManager_WithEShopData_Interface;
use JetApplication\Admin_Managers_Entity_Edit_WithEShopData;
use JetApplication\Entity_WithEShopData;
use JetApplication\Admin_Entity_WithEShopData_Interface;
use JetApplication\EShop;
use JetApplication\EShops;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;


abstract class Core_Admin_EntityManager_WithEShopData_Controller extends Admin_EntityManager_Controller
{
	
	/**
	 * @noinspection PhpDocFieldTypeMismatchInspection
	 * @var Application_Module|null|Admin_EntityManager_WithEShopData_Interface
	 */
	protected ?Application_Module $module = null;
	
	/**
	 * @var Entity_WithEShopData|Admin_Entity_WithEShopData_Interface|null
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
	
	protected function getDescriptionMode(): bool
	{
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
			$this->getEditorManager()->renderAdd(
				common_data_fields_renderer: $this->getAddCommonFieldsRenderer(),
				eshop_data_fields_renderer: $this->getAddEshopDataFieldsRenderer(),
				description_fields_renderer: $this->getAddDescriptionFieldsRenderer(),
			)
		);
		
	}
	
	protected function getEditToolbarRenderer() : ?Closure
	{
		return function() {
			echo $this->view->render('edit/toolbar');
		};
	}
	
	protected function getEditCommonDataFieldsRenderer() : ?Closure
	{
		return function() {
			echo $this->view->render('edit/main/common-form-fields');
		};
	}
	
	protected function getEditEshopDataFieldsRenderer() : ?Closure
	{
		if($this->getDescriptionMode()) {
			return null;
		}
		
		return function( EShop $eshop ) {
			$this->view->setVar('eshop', $eshop);
			echo $this->view->render('edit/main/shop-data-form-fields');
		};
	}
	
	protected function getEditDescriptionFieldsRenderer() : ?Closure
	{
		if(!$this->getDescriptionMode()) {
			return null;
		}
		
		return function( Locale $locale, string $locale_str ) {
			$this->view->setVar('locale', $locale);
			$this->view->setVar('locale_str', $locale_str);
			echo $this->view->render('edit/main/description-form-fields');
		};
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
				common_data_fields_renderer: $this->getEditCommonDataFieldsRenderer(),
				toolbar_renderer: $this->getEditToolbarRenderer(),
				eshop_data_fields_renderer: $this->getEditEshopDataFieldsRenderer(),
				description_fields_renderer: $this->getEditDescriptionFieldsRenderer()
			)
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
	
	
	public function getEditorManager(): Admin_Managers_Entity_Edit_WithEShopData|Application_Module
	{
		if(!$this->editor_manager) {
			$this->editor_manager = Admin_Managers::EntityEdit_WithEShopData();
			$this->editor_manager->init(
				$this->current_item,
				$this->getListing(),
				$this->tabs
			);
		}
		
		return $this->editor_manager;
	}
	
}