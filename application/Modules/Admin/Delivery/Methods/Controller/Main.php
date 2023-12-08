<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Methods;

use Jet\Logger;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;
use JetApplication\Delivery_Method;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\MVC_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetApplication\Delivery_Method_ShopData;
use JetApplication\Shops;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	/**
	 * @var ?MVC_Controller_Router_AddEditDelete
	 */
	protected ?MVC_Controller_Router_AddEditDelete $router = null;

	/**
	 * @var ?Delivery_Method
	 */
	protected ?Delivery_Method $delivery_method = null;

	/**
	 *
	 * @return MVC_Controller_Router_AddEditDelete
	 */
	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->delivery_method = Delivery_Method::get($id));
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => Main::ACTION_GET,
					'add'    => Main::ACTION_ADD,
					'edit'   => Main::ACTION_UPDATE,
					'delete' => Main::ACTION_DELETE,
				]
			);

			$GET = Http_Request::GET();
			if($GET->getString('id')) {
				$this->delivery_method = Delivery_Method::get( $GET->getString('id') );
			}
			$action = $GET->getString('action');

			$this->router->getAction('view')->setResolver( function() use ($action) {
				if(
					!$this->delivery_method ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );


			$this->router->getAction('edit')->setResolver( function() use ($action) {
				if(
					!$this->delivery_method ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('edit_images', Main::ACTION_UPDATE)->setResolver( function() use ($action) {
				if(
					!$this->delivery_method ||
					$this->_getEditTabs()->getSelectedTabId()!='images' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('view_images', Main::ACTION_GET)->setResolver( function() use ($action) {
				if(
					!$this->delivery_method ||
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
		if(!$this->delivery_method) {
			return null;
		}

		$_tabs = [
			'main'             => Tr::_('Main data'),
			'images'           => Tr::_('Images'),
		];


		return UI::tabs(
			$_tabs,
			function($page_id) {
				return '?id='.$this->delivery_method->getCode().'&page='.$page_id;
			},
			Http_Request::GET()->getString('page')
		);

	}


	/**
	 * @param string $current_label
	 */
	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();

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

		$this->view->setVar( 'filter_form', $listing->getFilterForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

		$this->output( 'list' );
	}

	/**
	 *
	 */
	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Delivery Method' ) );

		$delivery_method = new Delivery_Method();


		$form = $delivery_method->getAddForm();

		if( $delivery_method->catchAddForm() ) {
			$delivery_method->save();

			Logger::success(
				'delivery_method_created',
				'Delivery method '.$delivery_method->getInternalName().' ('.$delivery_method->getCode().') created',
				$delivery_method->getCode(),
				$delivery_method->getInternalName(),
				$delivery_method
			);

			UI_messages::success(
				Tr::_( 'Delivery Method <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$delivery_method->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_method', $delivery_method );

		$this->output( 'edit/main' );

	}



	/**
	 *
	 */
	public function edit_Action() : void
	{
		Application_Admin::handleUploadTooLarge();

		$delivery_method = $this->delivery_method;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit delivery method <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] ) );

		$form = $delivery_method->getEditForm();

		if( $delivery_method->catchEditForm() ) {

			$delivery_method->save();
			Logger::success(
				'delivery_method_updated',
				'Delivery method '.$delivery_method->getInternalName().' ('.$delivery_method->getCode().') updated',
				$delivery_method->getCode(),
				$delivery_method->getInternalName(),
				$delivery_method
			);

			UI_messages::success(
				Tr::_( 'Delivery Method <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_method', $delivery_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/main' );

	}

	/**
	 *
	 */
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		$delivery_method = $this->delivery_method;
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit delivery method <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] ) );

		foreach(Shops::getList() as $shop) {
			$delivery_method->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'Delivery method',
				object_id: $delivery_method->getCode(),
				object_name: $delivery_method->getInternalName(),
				upload_event: 'delivery_method_image_uploaded',
				delete_event: 'delivery_method_image_deleted'
			);
		}

		$this->view->setVar( 'delivery_method', $delivery_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/images' );

	}


	/**
	 *
	 */
	public function view_Action() : void
	{
		$delivery_method = $this->delivery_method;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delivery Method detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] )
		);

		$form = $delivery_method->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_method', $delivery_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/main' );

	}

	/**
	 *
	 */
	public function view_images_Action() : void
	{
		$delivery_method = $this->delivery_method;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delivery Method detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] )
		);

		Application_Admin::handleUploadTooLarge();
		$delivery_method = $this->delivery_method;
		foreach(Shops::getList() as $shop) {
			$shop_data = $delivery_method->getShopData( $shop );

			foreach( Delivery_Method_ShopData::getImageClasses() as $image_class=> $image_class_name ) {
				$shop_data->getImageUploadForm($image_class)->setIsReadonly();
				$shop_data->getImageDeleteForm($image_class)->setIsReadonly();

			}
		}


		$this->view->setVar( 'delivery_method', $delivery_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/images' );

	}


	/**
	 *
	 */
	public function delete_Action() : void
	{
		$delivery_method = $this->delivery_method;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete delivery method  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$delivery_method->delete();
			Logger::success(
				'delivery_method_deleted',
				'Delivery method '.$delivery_method->getInternalName().' ('.$delivery_method->getCode().') deleted',
				$delivery_method->getCode(),
				$delivery_method->getInternalName(),
				$delivery_method
			);

			UI_messages::info(
				Tr::_( 'Delivery Method <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $delivery_method->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'delivery_method', $delivery_method );

		$this->output( 'delete-confirm' );
	}

}