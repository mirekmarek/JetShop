<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\Properties;

use Jet\Factory_MVC;
use Jet\MVC_Controller_Router;
use Jet\UI;
use JetApplication\Application_Admin;

use Jet\MVC_Controller_Default;
use Jet\UI_messages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Logger;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	protected ?MVC_Controller_Router $router = null;

	protected ?Property $property = null;
	
	protected ?Property_Options_Option $option = null;
	
	protected ?Listing $listing = null;
	
	/**
	 *
	 * @return MVC_Controller_Router
	 */
	public function getControllerRouter() : MVC_Controller_Router
	{
		if( !$this->router ) {
			$GET = Http_Request::GET();
			
			$property_id = $GET->getInt( 'id' );
			$action = $GET->getString('action');
			$selected_tab = '';
			
			if($property_id) {
				$this->property = Property::get( $property_id );
				
				if($this->property) {
					$this->property->setEditable(Main::getCurrentUserCanEdit());
					
					$_tabs = [
						'main'   => Tr::_( 'Main data' ),
						'images' => Tr::_( 'Images' ),
					];
					
					if($this->property->getType()==Property::PROPERTY_TYPE_OPTIONS) {
						$option_id = $GET->getInt( 'option_id' );
						if($option_id) {
							$this->option = $this->property->getOption( $option_id );
							$this->option?->setEditable(Main::getCurrentUserCanEdit());
						}
						
						if( !$this->option ) {
							$_tabs['options'] = Tr::_( 'Options' );
						}
					}
					
					
					
					$tabs = UI::tabs(
						$_tabs,
						function($page_id) {
							return Http_Request::currentURI(['page'=>$page_id]);
						},
						Http_Request::GET()->getString('page', 'main')
					);
					
					$selected_tab = $tabs->getSelectedTabId();
					
					$this->view->setVar('tabs', $tabs);
					
				}
			}
			
			
			
			
			$this->router = new MVC_Controller_Router( $this );
			
			$this->router->setDefaultAction('listing', Main::ACTION_GET);
			
			$this->router->getAction('listing')->setResolver(function() use ($action) {
				return (!$this->property && !$action) ;
			});
			
			
			
			$this->router->addAction('add_property', Main::ACTION_ADD)
				->setResolver(function() use ($action) {
					return ($action=='add_property' && !$this->property);
				})
				->setURICreator(function( string $type=''  ) {
					return Http_Request::currentURI( ['action' => 'add_property', 'type'=>$type], ['id', 'option_id', 'page'] );
				});
			$this->router->addAction('delete_property', Main::ACTION_DELETE)
				->setResolver(function() use ($action) {
					return $this->property && !$this->option && $action=='delete';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'action'=>'delete'], ['option_id', 'page'] );
				});
			
			$this->router->addAction('edit_property_main', Main::ACTION_GET)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property && !$this->option && $selected_tab=='main';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id], ['option_id', 'page','action'] );
				});
			
			$this->router->addAction('edit_property_images', Main::ACTION_GET)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property && !$this->option && $selected_tab=='images';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], ['option_id', 'action'] );
				});
			
			$this->router->addAction('edit_property_options', Main::ACTION_GET)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property && !$this->option && $selected_tab=='options' && !$action;
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'properties'], ['option_id', 'action'] );
				});
			
			$this->router->addAction('edit_property_options_sort', Main::ACTION_GET)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property && !$this->option && $selected_tab=='options' && $action=='sort_options';
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

		return $this->router;
	}
	
	
	
	protected function getListing() : Listing
	{
		if(!$this->listing) {
			$column_view = Factory_MVC::getViewInstance( $this->view->getScriptsDir().'list/column/' );
			$column_view->setController( $this );
			$filter_view = Factory_MVC::getViewInstance( $this->view->getScriptsDir().'list/filter/' );
			$filter_view->setController( $this );
			
			$this->listing = new Listing(
				column_view: $column_view,
				filter_view: $filter_view
			);
		}
		
		return $this->listing;
	}
	
	/**
	 *
	 */
	public function listing_Action() : void
	{
		$listing = $this->getListing();
		$listing->handle();
		
		$this->view->setVar( 'listing', $listing );
		
		$this->output( 'list' );
	}

	/**
	 *
	 */
	public function add_property_Action() : void
	{
		
		$type = Http_Request::GET()->getString( key: 'type', valid_values: array_keys(Property::getTypes()));
		if(!$type) {
			die();
		}
		
		$class_name = Property::class.'_'.$type;
		
		/**
		 * @var Property $property
		 */
		$property = new $class_name();
		
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Create a new Property (%TYPE%)', ['TYPE'=>$property->getTypeTitle()] ) );
		


		$form = $property->getAddForm();

		if( $property->catchAddForm() ) {
			$property->save();

			Logger::success(
				event: 'property_created',
				event_message: 'Property created',
				context_object_id: $property->getId(),
				context_object_name: $property->getInternalName(),
				context_object_data: $property
			);


			UI_messages::success(
				Tr::_( 'Property <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $property->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$property->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'property', $property );

		$this->output( 'add' );

	}

	public function edit_property_main_Action() : void
	{
		$property = $this->property;
		
		$property->handleActivation();

		Navigation_Breadcrumb::addURL( Tr::_( 'Edit property (%TYPE%) <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $property->getInternalName(), 'TYPE'=>$property->getTypeTitle() ] ) );

		$form = $property->getEditForm();

		if( $property->catchEditForm() ) {

			$property->save();

			Logger::success(
				event: 'property_updated',
				event_message: 'Property updated',
				context_object_id: $property->getId(),
				context_object_name: $property->getInternalName(),
				context_object_data: $property
			);

			UI_messages::success(
				Tr::_( 'Property <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $property->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'property', $property );

		$this->output( 'edit/main' );

	}
	
	public function edit_property_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$property = $this->property;
		$property->handleImages();
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit property (%TYPE%) <b>%ITEM_NAME%</b> - images', [ 'ITEM_NAME' => $property->getInternalName(), 'TYPE'=>$property->getTypeTitle() ] ) );
		
		
		$this->view->setVar( 'property', $property );
		$this->output( 'edit/images' );
	}
	
	public function edit_property_options_Action() : void
	{
		$property = $this->property;
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit property (%TYPE%) <b>%ITEM_NAME%</b> - options', [ 'ITEM_NAME' => $property->getInternalName(), 'TYPE'=>$property->getTypeTitle() ] ) );
		
		$new_option = new Property_Options_Option();
		
		if($new_option->catchAddForm()) {
			$property->addOption( $new_option );
			//TODO: okamzite aktivovat
			
			$property->save();
			
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
		$property = $this->property;
		
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
		$property = $this->property;
		$option = $this->option;
		
		Navigation_Breadcrumb::addURL(
			Tr::_( 'Edit property (%TYPE%) <b>%ITEM_NAME%</b> - options', [ 'ITEM_NAME' => $property->getInternalName(), 'TYPE'=>$property->getTypeTitle() ] ),
			Http_Request::currentURI(set_GET_params: ['page'=>'options'], unset_GET_params: ['option_id'])
		);
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit option <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $option->getInternalName() ] ) );
		
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
		
		$property = $this->property;
		$option = $this->option;
		
		Navigation_Breadcrumb::addURL(
			Tr::_( 'Edit property (%TYPE%) <b>%ITEM_NAME%</b> - options', [ 'ITEM_NAME' => $property->getInternalName(), 'TYPE'=>$property->getTypeTitle() ] ),
			Http_Request::currentURI(set_GET_params: ['page'=>'options'], unset_GET_params: ['option_id'])
		);
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit option <b>%ITEM_NAME%</b> - images', [ 'ITEM_NAME' => $option->getInternalName() ] ) );
		
		$option->handleImages();
		
		$this->view->setVar( 'property', $property );
		$this->view->setVar( 'option', $option );
		
		$this->output( 'edit/option/images' );
	}
	
	
	
	public function delete_property_Action() : void
	{
		$property = $this->property;

		Navigation_Breadcrumb::addURL(
			Tr::_( 'Delete property  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $property->getInternalName() ] )
		);
		$this->view->setVar( 'property', $property );
		
		if($property->isItPossibleToDelete( $kinds )) {
			if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
				$property->delete();
				
				Logger::success(
					event: 'property_deleted',
					event_message: 'Property deleted',
					context_object_id: $property->getId(),
					context_object_name: $property->getInternalName(),
					context_object_data: $property
				);
				
				UI_messages::info(
					Tr::_( 'Property <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $property->getInternalName() ] )
				);
				
				Http_Headers::reload([], ['action', 'id']);
			}
			$this->output( 'delete/confirm' );
			
		} else {
			$this->view->setVar( 'kinds', $kinds );
			$this->output( 'delete/not-possible' );
		}



	}
}