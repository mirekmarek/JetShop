<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\Catalog\Properties;


use JetApplication\EShopEntity_Admin_Interface;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Application_Admin;
use JetApplication\Property_Options_Option;
use JetApplication\EShopEntity_WithEShopData;
use JetApplication\Property;

use Jet\UI_messages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

class Controller_Main extends Admin_EntityManager_Controller
{
	protected ?Property_Options_Option $option = null;
	
	public function getCustomTabs(): array
	{
		$_tabs = [];
		
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
		$this->listing_manager->addColumn( new Listing_Column_Type() );
		$this->listing_manager->addColumn( new Listing_Column_IsFilter() );
		$this->listing_manager->addColumn( new Listing_Column_IsDefaultFilter() );
		$this->listing_manager->addColumn( new Listing_Column_FilterPriority() );
		
		$this->listing_manager->addFilter( new Listing_Filter_Type() );
		$this->listing_manager->addFilter( new Listing_Filter_IsFilter() );
		$this->listing_manager->addFilter( new Listing_Filter_IsDefaultFilter() );
		
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'internal_name',
			'type',
			'is_filter',
			'is_default_filter',
			'filter_priority',
			'internal_code',
			'internal_notes',
		]);
		
		$this->listing_manager->setCreateBtnRenderer( function() : string {
			return $this->view->render('create_buttons');
		} );
	}
	
	protected function newItemFactory() : EShopEntity_WithEShopData|EShopEntity_Admin_Interface
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

		Http_Headers::reload(unset_GET_params: ['action']);
		
	}
	
	public function edit_property_option_main_Action() : void
	{
		
		
		$property = $this->current_item;
		$option = $this->option;
		
		$this->edit_main_handleActivation( $option );
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Options' ),
			Http_Request::currentURI(set_GET_params: ['page'=>'options'], unset_GET_params: ['option_id'])
		);
		
		Navigation_Breadcrumb::addURL( $option->getInternalName() );
		
		$form = $option->getEditForm();
		
		if( $option->catchEditForm() ) {
			
			$option->save();
			
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