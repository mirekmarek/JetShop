<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Services\Services;

use Jet\UI;
use Jet\UI_tabs;
use JetShop\Application_Admin;
use JetShop\Services_Service;

use Jet\Mvc_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\Mvc_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetShop\Services_Service_ShopData;
use JetShop\Shops;
use JetShopModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{

	/**
	 * @var ?Mvc_Controller_Router_AddEditDelete
	 */
	protected ?Mvc_Controller_Router_AddEditDelete $router = null;

	/**
	 * @var ?Services_Service
	 */
	protected ?Services_Service $service = null;

	/**
	 *
	 * @return Mvc_Controller_Router_AddEditDelete
	 */
	public function getControllerRouter() : Mvc_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new Mvc_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->service = Services_Service::get($id));
				},
				[
					'listing'=> Main::ACTION_GET_SERVICE,
					'view'   => Main::ACTION_GET_SERVICE,
					'add'    => Main::ACTION_ADD_SERVICE,
					'edit'   => Main::ACTION_UPDATE_SERVICE,
					'delete' => Main::ACTION_DELETE_SERVICE,
				]
			);

			$GET = Http_Request::GET();
			if($GET->getString('id')) {
				$this->service = Services_Service::get( $GET->getString('id') );
			}
			$action = $GET->getString('action');

			$this->router->getAction('view')->setResolver( function() use ($action) {
				if(
					!$this->service ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );


			$this->router->getAction('edit')->setResolver( function() use ($action) {
				if(
					!$this->service ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('edit_images', Main::ACTION_UPDATE_SERVICE)->setResolver( function() use ($action) {
				if(
					!$this->service ||
					$this->_getEditTabs()->getSelectedTabId()!='images' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('view_images', Main::ACTION_GET_SERVICE)->setResolver( function() use ($action) {
				if(
					!$this->service ||
					$this->_getEditTabs()->getSelectedTabId()!='images' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );


		}

		return $this->router;
	}


	protected function _getEditTabs() : ?UI_tabs
	{
		if(!$this->service) {
			return null;
		}

		$_tabs = [
			'main'             => Tr::_('Main data'),
			'images'           => Tr::_('Images'),
		];


		return UI::tabs(
			$_tabs,
			function($page_id) {
				return '?id='.$this->service->getCode().'&page='.$page_id;
			},
			Http_Request::GET()->getString('page')
		);

	}


	/**
	 * @param string $current_label
	 */
	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		UI_module::initBreadcrumb();

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}

	/**
	 *
	 */
	public function listing_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$listing = new Listing();
		$listing->handle();

		$this->view->setVar( 'filter_form', $listing->filter_getForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

		$this->output( 'list' );
	}

	/**
	 *
	 */
	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Service' ) );

		$service = new Services_Service();


		$form = $service->getAddForm();

		if( $service->catchAddForm() ) {
			$service->save();

			$this->logAllowedAction( 'Service created', $service->getCode(), $service->getInternalName(), $service );

			UI_messages::success(
				Tr::_( 'Service <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $service->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$service->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'service', $service );

		$this->output( 'edit/main' );

	}



	/**
	 *
	 */
	public function edit_Action() : void
	{
		Application_Admin::handleUploadTooLarge();

		$service = $this->service;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit service <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $service->getInternalName() ] ) );

		$form = $service->getEditForm();

		if( $service->catchEditForm() ) {

			$service->save();
			$this->logAllowedAction( 'Service updated', $service->getCode(), $service->getInternalName(), $service );

			UI_messages::success(
				Tr::_( 'Service <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $service->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'service', $service );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/main' );

	}

	/**
	 *
	 */
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		$service = $this->service;
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit service <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $service->getInternalName() ] ) );

		foreach(Shops::getList() as $shop) {
			$shop_id = $shop->getId();
			$shop_name = $shop->getName();
			$shop_data = $service->getShopData( $shop_id );

			foreach( Services_Service_ShopData::getImageClasses() as $image_class=> $image_class_name ) {
				$shop_data->catchImageWidget(
					$image_class,
					function() use ($image_class, $service, $shop_id, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'service image '.$image_class.' uploaded', $service->getCode().':'.$shop_id, $service->getCode().' - '.$shop_name );

					},
					function() use ($image_class, $service, $shop_id, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'service image '.$image_class.' deleted', $service->getCode().':'.$shop_id, $service->getCode().' - '.$shop_name );
					}
				);

			}
		}

		$this->view->setVar( 'service', $service );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/images' );

	}


	/**
	 *
	 */
	public function view_Action() : void
	{
		$service = $this->service;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Service detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $service->getInternalName() ] )
		);

		$form = $service->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'service', $service );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/main' );

	}

	/**
	 *
	 */
	public function view_images_Action() : void
	{
		$service = $this->service;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Service detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $service->getInternalName() ] )
		);

		Application_Admin::handleUploadTooLarge();
		$service = $this->service;
		foreach(Shops::getList() as $shop) {
			$shop_data = $service->getShopData( $shop->getId() );

			foreach( Services_Service_ShopData::getImageClasses() as $image_class=> $image_class_name ) {
				$shop_data->getImageUploadForm($image_class)->setIsReadonly();
				$shop_data->getImageDeleteForm($image_class)->setIsReadonly();

			}
		}


		$this->view->setVar( 'service', $service );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/images' );

	}


	/**
	 *
	 */
	public function delete_Action() : void
	{
		$service = $this->service;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete service  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $service->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$service->delete();
			$this->logAllowedAction( 'Service deleted', $service->getCode(), $service->getInternalName(), $service );

			UI_messages::info(
				Tr::_( 'Service <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $service->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'service', $service );

		$this->output( 'delete-confirm' );
	}

}