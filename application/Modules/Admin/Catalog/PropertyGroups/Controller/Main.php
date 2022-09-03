<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Catalog\PropertyGroups;

use Jet\Application;
use Jet\UI;
use JetShop\Application_Admin;
use JetShop\Fulltext_Index_Internal_PropertyGroup;
use JetShop\PropertyGroup;

use Jet\MVC_Controller_Router;
use Jet\MVC_Controller_Default;
use Jet\UI_messages;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;
use Jet\Logger;
use JetShop\Shops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 * @var ?MVC_Controller_Router
	 */
	protected ?MVC_Controller_Router $router = null;

	/**
	 * @var ?PropertyGroup
	 */
	protected ?PropertyGroup $property_group = null;

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
			$this->router->addAction( 'whisper' )->setResolver(function() use ($GET) {
				return $GET->exists('whisper');
			});
			
			$this->router->setDefaultAction('listing', Main::ACTION_GET_PROPERTY_GROUP);
			$this->router->getAction('listing')->setResolver(function() use ($action) {
				return (!$this->property_group && !$action) ;
			});
			
			
			
			$this->router->addAction('add', Main::ACTION_ADD_PROPERTY_GROUP)
				->setResolver(function() use ($action) {
					return ($action=='add' && !$this->property_group);
				})
				->setURICreator(function() {
					return Http_Request::currentURI( ['action' => 'add'], ['id', 'page'] );
				});
			
			$this->router->addAction('delete', Main::ACTION_DELETE_PROPERTY_GROUP)
				->setResolver(function() use ($action) {
					return $this->property_group && $action=='delete';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'action'=>'delete'], ['page'] );
				});
			
			
			$this->router->addAction('edit_main', Main::ACTION_UPDATE_PROPERTY_GROUP)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property_group && $selected_tab=='main' && $action=='';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id], ['page','action'] );
				});
			
			$this->router->addAction('edit_images', Main::ACTION_UPDATE_PROPERTY_GROUP)
				->setResolver(function() use ($action, $selected_tab) {
					return $this->property_group && $selected_tab=='images' && $action=='';
				})
				->setURICreator(function( int $id ) {
					return Http_Request::currentURI( ['id'=>$id, 'page'=>'images'], ['action'] );
				});
			
			
		}

		return $this->router;
	}


	/**
	 *
	 */
	public function listing_Action() : void
	{
		$listing = new Listing();
		$listing->handle();

		$this->view->setVar( 'filter_form', $listing->getFilterForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

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
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit property group <b>%ITEM_NAME%</b> - images', [ 'ITEM_NAME' => $property_group->getInternalName() ] ) );
		
		foreach(Shops::getList() as $shop) {
			$property_group->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'property group image',
				object_id: $property_group->getId(),
				object_name: $property_group->getInternalName(),
				upload_event: 'property_group_image_uploaded',
				delete_event: 'property_group_image_deleted'
			);
		}
		
		
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
	
	public function whisper_Action() : void
	{
		$GET = Http_Request::GET();
		
		
		$result = Fulltext_Index_Internal_PropertyGroup::search(
			search_string: $GET->getString('whisper'),
			only_active: $GET->getBool('only_active'),
		);
		
		$this->view->setVar('result', $result);
		echo $this->view->render('search_whisperer_result');
		
		Application::end();
	}
	
}