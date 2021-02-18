<?php
namespace JetShopModule\Admin\Delivery\Deadlines;


use Jet\Mvc_Controller_Router_AddEditDelete;
use Jet\UI;
use Jet\UI_messages;

use Jet\Mvc_Controller_Default;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use Jet\UI_tabs;
use JetShop\Delivery_Deadline_ShopData;
use JetShop\Shops;
use JetShop\Application_Admin;
use JetShop\Delivery_Deadline;

use JetShopModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{

	protected ?Mvc_Controller_Router_AddEditDelete $router = null;

	protected ?Delivery_Deadline $delivery_deadline = null;

	public function getControllerRouter() : Mvc_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new Mvc_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->delivery_deadline = Delivery_Deadline::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET_DELIVERY_DEADLINE,
					'view'   => Main::ACTION_GET_DELIVERY_DEADLINE,
					'add'    => Main::ACTION_ADD_DELIVERY_DEADLINE,
					'edit'   => Main::ACTION_UPDATE_DELIVERY_DEADLINE,
					'delete' => Main::ACTION_DELETE_DELIVERY_DEADLINE,
				]
			);

			$GET = Http_Request::GET();
			if($GET->getString('id')) {
				$this->delivery_deadline = Delivery_Deadline::get( $GET->getString('id') );
			}
			$action = $GET->getString('action');

			$this->router->getAction('view')->setResolver( function() use ($action) {
				if(
					!$this->delivery_deadline ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );


			$this->router->getAction('edit')->setResolver( function() use ($action) {
				if(
					!$this->delivery_deadline ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('edit_images', Main::ACTION_UPDATE_DELIVERY_DEADLINE)->setResolver( function() use ($action) {
				if(
					!$this->delivery_deadline ||
					$this->_getEditTabs()->getSelectedTabId()!='images' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('view_images', Main::ACTION_GET_DELIVERY_DEADLINE)->setResolver( function() use ($action) {
				if(
					!$this->delivery_deadline ||
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
		if(!$this->delivery_deadline) {
			return null;
		}

		$_tabs = [
			'main'             => Tr::_('Main data'),
			'images'           => Tr::_('Images'),
		];


		return UI::tabs(
			$_tabs,
			function($page_id) {
				return '?id='.$this->delivery_deadline->getCode().'&page='.$page_id;
			},
			Http_Request::GET()->getString('page')
		);

	}


	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		UI_module::initBreadcrumb();

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}

	public function listing_Action() : void
	{
		$this->_setBreadcrumbNavigation();

		$listing = new Listing();
		$listing->handle();

		$this->view->setVar( 'filter_form', $listing->filter_getForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

		$this->output( 'list' );
	}

	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new delivery deadline' ) );

		$delivery_deadline = new Delivery_Deadline();


		$form = $delivery_deadline->getAddForm();

		if( $delivery_deadline->catchAddForm() ) {
			$delivery_deadline->save();

			$this->logAllowedAction( 'Delivery deadline created', $delivery_deadline->getCode(), $delivery_deadline->getInternalName(), $delivery_deadline );

			UI_messages::success(
				Tr::_( 'Delivery deadline <b>%NAME%</b> has been created', [ 'NAME' => $delivery_deadline->getInternalName() ] )
			);

			Http_Headers::reload( ['code'=>$delivery_deadline->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_deadline', $delivery_deadline );

		$this->output( 'add' );

	}

	public function edit_Action() : void
	{
		$delivery_deadline = $this->delivery_deadline;

		Application_Admin::handleUploadTooLarge();

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit delivery deadline <b>%NAME%</b>', [ 'NAME' => $delivery_deadline->getInternalName() ] ) );

		$form = $delivery_deadline->getEditForm();

		if( $delivery_deadline->catchEditForm() ) {

			$delivery_deadline->save();
			$this->logAllowedAction( 'Delivery deadline updated', $delivery_deadline->getCode(), $delivery_deadline->getInternalName(), $delivery_deadline );

			UI_messages::success(
				Tr::_( 'Delivery deadline <b>%NAME%</b> has been updated', [ 'NAME' => $delivery_deadline->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );
		$this->view->setVar( 'delivery_deadline', $delivery_deadline );

		$this->output( 'edit/main' );

	}

	/**
	 *
	 */
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		$delivery_deadline = $this->delivery_deadline;
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit delivery deadline <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_deadline->getInternalName() ] ) );

		foreach(Shops::getList() as $shop) {
			$shop_code = $shop->getCode();
			$shop_name = $shop->getName();
			$shop_data = $delivery_deadline->getShopData( $shop_code );

			foreach( Delivery_Deadline_ShopData::getImageClasses() as $image_class=> $image_class_name ) {
				$shop_data->catchImageWidget(
					$image_class,
					function() use ($image_class, $delivery_deadline, $shop_code, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'delivery deadline image '.$image_class.' uploaded', $delivery_deadline->getCode().':'.$shop_code, $delivery_deadline->getCode().' - '.$shop_name );

					},
					function() use ($image_class, $delivery_deadline, $shop_code, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'delivery deadline image '.$image_class.' deleted', $delivery_deadline->getCode().':'.$shop_code, $delivery_deadline->getCode().' - '.$shop_name );
					}
				);

			}
		}

		$this->view->setVar( 'delivery_deadline', $delivery_deadline );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/images' );

	}


	public function view_Action() : void
	{
		$delivery_deadline = $this->delivery_deadline;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delivery deadline detail <b>%NAME%</b>', [ 'NAME' => $delivery_deadline->getInternalName() ] )
		);

		$form = $delivery_deadline->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );
		$this->view->setVar( 'delivery_deadline', $delivery_deadline );

		$this->output( 'edit/main' );

	}

	public function delete_Action() : void
	{
		$delivery_deadline = $this->delivery_deadline;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete delivery deadline <b>%NAME%</b>', [ 'NAME' => $delivery_deadline->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$delivery_deadline->delete();
			$this->logAllowedAction( 'Delivery deadline deleted', $delivery_deadline->getCode(), $delivery_deadline->getInternalName(), $delivery_deadline );

			UI_messages::info(
				Tr::_( 'Delivery deadline <b>%NAME%</b> has been deleted', [ 'NAME' => $delivery_deadline->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'delivery_deadline', $delivery_deadline );

		$this->output( 'delete-confirm' );
	}

}