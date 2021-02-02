<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Payment\Methods;

use Jet\UI;
use Jet\UI_tabs;
use JetShop\Application_Admin;
use JetShop\Payment_Method;

use Jet\Mvc_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\Mvc_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetShop\Payment_Method_ShopData;
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
	 * @var ?Payment_Method
	 */
	protected ?Payment_Method $payment_method = null;

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
					return (bool)($this->payment_method = Payment_Method::get($id));
				},
				[
					'listing'=> Main::ACTION_GET_PAYMENT_METHOD,
					'view'   => Main::ACTION_GET_PAYMENT_METHOD,
					'add'    => Main::ACTION_ADD_PAYMENT_METHOD,
					'edit'   => Main::ACTION_UPDATE_PAYMENT_METHOD,
					'delete' => Main::ACTION_DELETE_PAYMENT_METHOD,
				]
			);

			$GET = Http_Request::GET();
			if($GET->getString('id')) {
				$this->payment_method = Payment_Method::get( $GET->getString('id') );
			}
			$action = $GET->getString('action');

			$this->router->getAction('view')->setResolver( function() use ($action) {
				if(
					!$this->payment_method ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );


			$this->router->getAction('edit')->setResolver( function() use ($action) {
				if(
					!$this->payment_method ||
					$this->_getEditTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('edit_images', Main::ACTION_UPDATE_PAYMENT_METHOD)->setResolver( function() use ($action) {
				if(
					!$this->payment_method ||
					$this->_getEditTabs()->getSelectedTabId()!='images' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('view_images', Main::ACTION_GET_PAYMENT_METHOD)->setResolver( function() use ($action) {
				if(
					!$this->payment_method ||
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
		if(!$this->payment_method) {
			return null;
		}

		$_tabs = [
			'main'             => Tr::_('Main data'),
			'images'           => Tr::_('Images'),
		];


		return UI::tabs(
			$_tabs,
			function($page_id) {
				return '?id='.$this->payment_method->getCode().'&page='.$page_id;
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Payment Method' ) );

		$payment_method = new Payment_Method();


		$form = $payment_method->getAddForm();

		if( $payment_method->catchAddForm() ) {
			$payment_method->save();

			$this->logAllowedAction( 'Payment Method created', $payment_method->getCode(), $payment_method->getInternalName(), $payment_method );

			UI_messages::success(
				Tr::_( 'Payment Method <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $payment_method->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$payment_method->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'payment_method', $payment_method );

		$this->output( 'edit/main' );

	}



	/**
	 *
	 */
	public function edit_Action() : void
	{
		Application_Admin::handleUploadTooLarge();

		$payment_method = $this->payment_method;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit payment method <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $payment_method->getInternalName() ] ) );

		$form = $payment_method->getEditForm();

		if( $payment_method->catchEditForm() ) {

			$payment_method->save();
			$this->logAllowedAction( 'Payment Method updated', $payment_method->getCode(), $payment_method->getInternalName(), $payment_method );

			UI_messages::success(
				Tr::_( 'Payment Method <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $payment_method->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/main' );

	}

	/**
	 *
	 */
	public function edit_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		$payment_method = $this->payment_method;
		$this->_setBreadcrumbNavigation( Tr::_( 'Edit payment method <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $payment_method->getInternalName() ] ) );

		foreach(Shops::getList() as $shop) {
			$shop_id = $shop->getId();
			$shop_name = $shop->getName();
			$shop_data = $payment_method->getShopData( $shop_id );

			foreach( Payment_Method_ShopData::getImageClasses() as $image_class=> $image_class_name ) {
				$shop_data->catchImageWidget(
					$image_class,
					function() use ($image_class, $payment_method, $shop_id, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'payment method image '.$image_class.' uploaded', $payment_method->getCode().':'.$shop_id, $payment_method->getCode().' - '.$shop_name );

					},
					function() use ($image_class, $payment_method, $shop_id, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'payment method image '.$image_class.' deleted', $payment_method->getCode().':'.$shop_id, $payment_method->getCode().' - '.$shop_name );
					}
				);

			}
		}

		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/images' );

	}


	/**
	 *
	 */
	public function view_Action() : void
	{
		$payment_method = $this->payment_method;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Payment Method detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $payment_method->getInternalName() ] )
		);

		$form = $payment_method->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/main' );

	}

	/**
	 *
	 */
	public function view_images_Action() : void
	{
		$payment_method = $this->payment_method;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Payment Method detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $payment_method->getInternalName() ] )
		);

		Application_Admin::handleUploadTooLarge();
		$payment_method = $this->payment_method;
		foreach(Shops::getList() as $shop) {
			$shop_data = $payment_method->getShopData( $shop->getId() );

			foreach( Payment_Method_ShopData::getImageClasses() as $image_class=> $image_class_name ) {
				$shop_data->getImageUploadForm($image_class)->setIsReadonly();
				$shop_data->getImageDeleteForm($image_class)->setIsReadonly();

			}
		}


		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/images' );

	}


	/**
	 *
	 */
	public function delete_Action() : void
	{
		$payment_method = $this->payment_method;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete payment method  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $payment_method->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$payment_method->delete();
			$this->logAllowedAction( 'Payment Method deleted', $payment_method->getCode(), $payment_method->getInternalName(), $payment_method );

			UI_messages::info(
				Tr::_( 'Payment Method <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $payment_method->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'payment_method', $payment_method );

		$this->output( 'delete-confirm' );
	}

}