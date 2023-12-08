<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Catalog\PropertyGroups;

use Jet\Factory_MVC;
use Jet\UI;
use JetApplication\Application_Admin;

use Jet\MVC_Controller_Router;
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

	protected ?PropertyGroup $property_group = null;
	
	protected ?Listing $listing = null;
	

	/**
	 *
	 * @return MVC_Controller_Router
	 */
	public function getControllerRouter() : MVC_Controller_Router
	{
		if( !$this->router ) {
			$GET = Http_Request::GET();
			
			$property_group_id = $GET->getInt( 'id' );
			$action = $GET->getString('action');
			$selected_tab = '';
			
			if($property_group_id) {
				$this->property_group = PropertyGroup::get( $property_group_id );
				
				if($this->property_group) {
					$this->property_group->setEditable(Main::getCurrentUserCanEdit());
					
					$_tabs = [
						'main'   => Tr::_( 'Main data' ),
						'images' => Tr::_( 'Images' ),
					];
					
					
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
				return (!$this->property_group && !$action) ;
			});
			
			
			
			$this->router->addAction('add', Main::ACTION_ADD)
				->setResolver(function() use ($action) {
					return ($action=='add' && !$this->property_group);
				})
				->setURICreator(function() {
					return Http_Request::currentURI( ['action' => 'add'], ['id', 'page'] );
				});
			
			$this->router->addAction('delete', Main::ACTION_DELETE)
				->setResolver(function() use ($action) {
					return $this->property_group && $action=='delete';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'action'=>'delete'], ['page'] );
				});
			
			
			$this->router->addAction('edit_main', Main::ACTION_UPDATE)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property_group && $selected_tab=='main' && $action=='';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id], ['page','action'] );
				});
			
			$this->router->addAction('edit_images', Main::ACTION_UPDATE)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property_group && $selected_tab=='images' && $action=='';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], ['action'] );
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
	public function add_Action() : void
	{
		Navigation_Breadcrumb::addURL( Tr::_( 'Create a new Property Group' ) );

		$property_group = new PropertyGroup();


		$form = $property_group->getAddForm();

		if( $property_group->catchAddForm() ) {
			$property_group->save();

			Logger::success(
				event: 'property_group_created',
				event_message: 'Property Group created',
				context_object_id: $property_group->getId(),
				context_object_name: $property_group->getInternalName(),
				context_object_data: $property_group
			);


			UI_messages::success(
				Tr::_( 'Property Group <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $property_group->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$property_group->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'property_group', $property_group );

		$this->output( 'add' );

	}

	/**
	 *
	 */
	public function edit_main_Action() : void
	{
		$property_group = $this->property_group;
		
		$property_group->handleActivation();

		Navigation_Breadcrumb::addURL( Tr::_( 'Edit property group <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $property_group->getInternalName() ] ) );

		$form = $property_group->getEditForm();

		if( $property_group->catchEditForm() ) {

			$property_group->save();

			Logger::success(
				event: 'property_group_updated',
				event_message: 'Property Group updated',
				context_object_id: $property_group->getId(),
				context_object_name: $property_group->getInternalName(),
				context_object_data: $property_group
			);

			UI_messages::success(
				Tr::_( 'Property Group <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $property_group->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'property_group', $property_group );

		$this->output( 'edit/main' );

	}
	
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		$property_group = $this->property_group;
		$property_group->handleImages();
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit property group <b>%ITEM_NAME%</b> - images', [ 'ITEM_NAME' => $property_group->getInternalName() ] ) );
		
		$this->view->setVar( 'property_group', $property_group );
		$this->output( 'edit/images' );
	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$property_group = $this->property_group;

		Navigation_Breadcrumb::addURL(
			Tr::_( 'Delete property group  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $property_group->getInternalName() ] )
		);
		$this->view->setVar( 'property_group', $property_group );
		
		if($property_group->isItPossibleToDelete( $kinds )) {
			if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
				$property_group->delete();
				
				Logger::success(
					event: 'property_group_deleted',
					event_message: 'Property Group deleted',
					context_object_id: $property_group->getId(),
					context_object_name: $property_group->getInternalName(),
					context_object_data: $property_group
				);
				
				UI_messages::info(
					Tr::_( 'Property Group <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $property_group->getInternalName() ] )
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