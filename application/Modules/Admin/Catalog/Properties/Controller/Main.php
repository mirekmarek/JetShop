<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use JetApplication\Admin_Entity_WithShopData_Interface;
use JetApplication\Admin_Entity_WithShopData_Manager_Controller;
use JetApplication\Application_Admin;

use Jet\UI_messages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Logger;
use JetApplication\Entity_WithShopData;

class Controller_Main extends Admin_Entity_WithShopData_Manager_Controller
{
	protected ?Property_Options_Option $option = null;
	
	
	
	public function getTabs(): array
	{
		$_tabs = [
			'main'   => Tr::_( 'Main data' ),
			'images' => Tr::_( 'Images' ),
		];
		
		if(
			$this->current_item->getType()==Property::PROPERTY_TYPE_OPTIONS &&
			!$this->option
		) {
			$_tabs['options'] = Tr::_( 'Options' );
		}
		
		return $_tabs;
	}
	
	
	protected function currentItemGetter() : void
	{
		parent::currentItemGetter();
		
		if(
			$this->current_item &&
			$this->current_item->getType()==Property::PROPERTY_TYPE_OPTIONS
		) {
			$option_id = Http_Request::GET()->getInt( 'option_id' );
			if($option_id) {
				$this->option = $this->current_item->getOption( $option_id );
				$this->option?->setEditable(Main::getCurrentUserCanEdit());
			}
		}
		
	}
	
	protected function setupRouter( string $action, string $selected_tab ) : void
	{
		if(
			!$this->option &&
			$selected_tab!='options'
		) {
			parent::setupRouter( $action, $selected_tab );
			return;
		}
		
		$this->router->addAction('edit_property_options', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && !$this->option && $selected_tab=='options' && !$action;
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'properties'], ['option_id', 'action'] );
			});
		
		$this->router->addAction('edit_property_options_sort', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && !$this->option && $selected_tab=='options' && $action=='sort_options';
			});
		
		
		$this->router->addAction('edit_property_option_main', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->option && $selected_tab=='main' && !$action;
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['option_id'=>$id, 'page'=>'main'], ['action'] );
			});
		$this->router->addAction('edit_property_option_images', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->option && $selected_tab=='images' && !$action;
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['option_id'=>$id, 'page'=>'images'], ['action'] );
			});
	}
		
	
	public function setupListing() : void
	{
		$this->listing_manager->addColumn(
			new Listing_Column_Type()
		);
		
		$this->listing_manager->addFilter(
			new Listing_Filter_Type()
		);
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'type',
			'internal_name',
			'internal_code',
			'internal_notes',
		]);
		
		$this->listing_manager->setCreateBtnRenderer( function() : string {
			return $this->view->render('create_buttons');
		} );
	}
	
	protected function newItemFactory() : Entity_WithShopData|Admin_Entity_WithShopData_Interface
	{
		$type = Http_Request::GET()->getString( key: 'type', valid_values: array_keys(Property::getTypesScope()));
		if(!$type) {
			die();
		}
		
		$property = new Property();
		$property->setType( $type );
		
		return $property;
	}
	



	
	public function edit_property_options_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Options') );
		/**
		 * @var Property $property
		 */
		$property = $this->current_item;
		
		$new_option = new Property_Options_Option();
		
		if($new_option->catchAddForm()) {
			$property->addOption( $new_option );
			
			Logger::success(
				event: 'property_updated.option_added',
				event_message: 'Property updated - option added',
				context_object_id: $property->getId(),
				context_object_name: $property->getInternalName(),
				context_object_data: $property
			);
			
			UI_messages::success(
				Tr::_( 'Option <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $new_option->getInternalName() ] )
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar('new_option', $new_option);
		
		
		$this->view->setVar( 'property', $property );
		$this->output( 'edit/options' );
	}
	
	public function edit_property_options_sort_Action() : void
	{
		$property = $this->current_item;
		
		$sort = explode('|', Http_Request::POST()->getString('sort_order'));
		$property->sortOptions( $sort );
		$property->save();
		
		Logger::success(
			event: 'property_updated.options_sorted',
			event_message: 'Property updated - options sorted',
			context_object_id: $property->getId(),
			context_object_name: $property->getInternalName(),
			context_object_data: $property
		);
		
		Http_Headers::reload(unset_GET_params: ['action']);
		
	}
	
	public function edit_property_option_main_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Properties') );
		
		
		$property = $this->current_item;
		$option = $this->option;
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Options' ),
			Http_Request::currentURI(set_GET_params: ['page'=>'options'], unset_GET_params: ['option_id'])
		);
		
		Navigation_Breadcrumb::addURL( $option->getInternalName() );
		
		$form = $option->getEditForm();
		
		if( $option->catchEditForm() ) {
			
			$option->save();
			
			Logger::success(
				event: 'property_updated.option_update',
				event_message: 'Property updated - option updated',
				context_object_id: $option->getId(),
				context_object_name: $option->getInternalName(),
				context_object_data: $option
			);
			
			UI_messages::success(
				Tr::_( 'Option <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $option->getInternalName() ] )
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'property', $property );
		$this->view->setVar( 'option', $option );
		
		$this->output( 'edit/option/main' );
		
	}
	
	public function edit_property_option_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$property = $this->current_item;
		$option = $this->option;
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Options' ),
			Http_Request::currentURI(set_GET_params: ['page'=>'options'], unset_GET_params: ['option_id'])
		);
		
		Navigation_Breadcrumb::addURL( $option->getInternalName() .' - '.Tr::_('Images') );
		
		$option->handleImages();
		
		$this->view->setVar( 'property', $property );
		$this->view->setVar( 'option', $option );
		
		$this->output( 'edit/option/images' );
	}
	
}