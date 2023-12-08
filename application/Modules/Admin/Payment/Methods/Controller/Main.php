<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\Logger;
use Jet\UI;
use Jet\UI_tabs;
use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;
use JetApplication\Payment_Method;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\MVC_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetApplication\Payment_Method_Option;
use JetApplication\Payment_Method_ShopData;
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
	 * @var ?Payment_Method
	 */
	protected ?Payment_Method $payment_method = null;

	/**
	 * @var ?Payment_Method_Option
	 */
	protected ?Payment_Method_Option $payment_method_option = null;

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
					return (bool)($this->payment_method = Payment_Method::get($id));
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

			$this->router->addAction('edit_images', Main::ACTION_UPDATE)->setResolver( function() use ($action) {
				if(
					!$this->payment_method ||
					$this->_getEditTabs()->getSelectedTabId()!='images' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('view_images', Main::ACTION_GET)->setResolver( function() use ($action) {
				if(
					!$this->payment_method ||
					$this->_getEditTabs()->getSelectedTabId()!='images' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			if(
				$this->payment_method &&
				$this->_getEditTabs()->getSelectedTabId()=='options'
			) {
				if($GET->exists('option')) {
					$this->payment_method_option = $this->payment_method->getOption($GET->getString('option'));
				}
			}

			$this->router->addAction('options_list', Main::ACTION_GET)->setResolver( function() use ($action) {
				if(
					$this->payment_method_option ||
					!$this->payment_method ||
					$this->_getEditTabs()->getSelectedTabId()!='options' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );


			$this->router->addAction('options_create', Main::ACTION_UPDATE)->setResolver( function() use ($action) {
				if(
					!$this->payment_method ||
					$this->_getEditTabs()->getSelectedTabId()!='options' ||
					$action!='create_option'
				) {
					return false;
				}

				return true;
			} );


			$this->router->addAction('options_edit_main', Main::ACTION_UPDATE)->setResolver( function() use ($action) {
				if(
					!$this->payment_method_option ||
					$this->_getEditOptionTabs()->getSelectedTabId()!='main' ||
					$action!=''
				) {
					return false;
				}

				return true;
			} );

			$this->router->addAction('options_edit_images', Main::ACTION_UPDATE)->setResolver( function() use ($action) {
				if(
					!$this->payment_method_option ||
					$this->_getEditOptionTabs()->getSelectedTabId()!='images' ||
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
			'options'          => Tr::_('Options'),
		];


		return UI::tabs(
			$_tabs,
			function($page_id) {
				return '?id='.$this->payment_method->getCode().'&page='.$page_id;
			},
			Http_Request::GET()->getString('page')
		);
	}

	protected function _getEditOptionTabs() : ?UI_tabs
	{
		if(!$this->payment_method_option) {
			return null;
		}

		$_tabs = [
			'main'             => Tr::_('Main data'),
			'images'           => Tr::_('Images'),
		];


		return UI::tabs(
			$_tabs,
			function($page_id) {
				return '?id='.$this->payment_method->getCode().'&page=options&option='.$this->payment_method_option->getCode().'&option_page='.$page_id;
			},
			Http_Request::GET()->getString('option_page')
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Payment Method' ) );

		$payment_method = new Payment_Method();


		$form = $payment_method->getAddForm();

		if( $payment_method->catchAddForm() ) {
			$payment_method->save();

			Logger::success(
				'payment_method_created',
				'Payment method '.$payment_method->getInternalName().' ('.$payment_method->getCode().') created',
				$payment_method->getCode(),
				$payment_method->getInternalName(),
				$payment_method
			);

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
			Logger::success(
				'payment_method_updated',
				'Payment method '.$payment_method->getInternalName().' ('.$payment_method->getCode().') updated',
				$payment_method->getCode(),
				$payment_method->getInternalName(),
				$payment_method
			);

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
			$payment_method->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'Payment method',
				object_id: $payment_method->getCode(),
				object_name: $payment_method->getInternalName(),
				upload_event: 'payment_method_image_uploaded',
				delete_event: 'payment_method_image_deleted'
			);
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
			$shop_data = $payment_method->getShopData( $shop );

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
			Logger::success(
				'payment_method_deleted',
				'Payment method '.$payment_method->getInternalName().' ('.$payment_method->getCode().') deleted',
				$payment_method->getCode(),
				$payment_method->getInternalName(),
				$payment_method
			);

			UI_messages::info(
				Tr::_( 'Payment Method <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $payment_method->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'payment_method', $payment_method );

		$this->output( 'delete-confirm' );
	}



	public function options_list_Action() : void
	{
		$payment_method = $this->payment_method;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit payment method <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $payment_method->getInternalName() ] ) );


		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/options/list' );
	}

	public function options_create_Action() : void
	{
		$payment_method = $this->payment_method;
		
		Admin_Managers::UI()->initBreadcrumb();
		
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit payment method <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $payment_method->getInternalName() ] ), Http_Request::currentURL([], ['action']) );
		Navigation_Breadcrumb::addURL( Tr::_('New option') );

		$new_option = new Payment_Method_Option();

		if( $new_option->catchAddForm() ) {
			$payment_method->addOption( $new_option );
			$payment_method->save();

			Logger::success(
				'payment_method_updated',
				'Payment method '.$payment_method->getInternalName().' ('.$payment_method->getCode().') updated',
				$payment_method->getCode(),
				$payment_method->getInternalName(),
				$payment_method
			);

			UI_messages::success(
				Tr::_( 'Payment method <b>%NAME%</b> has been updated - option created', ['NAME' => $payment_method->getInternalName()] )
			);

			Http_Headers::movedTemporary( Http_Request::currentURI( [], ['action'] ) );

		}


		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'new_option', $new_option );
		$this->view->setVar( 'tabs', $this->_getEditTabs() );

		$this->output( 'edit/options/create' );
	}

	public function options_edit_main_Action() : void
	{

		$payment_method = $this->payment_method;
		$payment_method_option = $this->payment_method_option;
		
		Admin_Managers::UI()->initBreadcrumb();
		
		Navigation_Breadcrumb::addURL(
			Tr::_( 'Edit payment method <b>%NAME%</b>', [ 'NAME' => $payment_method->getInternalName() ] ),
			Http_Request::currentURI([], ['action', 'option'])
		);
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit option <b>%OPTION%</b>', ['OPTION'=>$payment_method_option->getShopData()->getTitle()] ) );

		if($payment_method_option->catchEditForm()) {
			$payment_method->save();

			Logger::success(
				'payment_method_updated',
				'Payment method '.$payment_method->getInternalName().' ('.$payment_method->getCode().') updated',
				$payment_method->getCode(),
				$payment_method->getInternalName(),
				$payment_method
			);

			UI_messages::success(
				Tr::_( 'Payment method <b>%NAME%</b> has been updated - option updated', [ 'NAME' => $payment_method->getInternalName() ] )
			);

			Http_Headers::movedTemporary( Http_Request::currentURI() );

		}



		$this->view->setVar( 'tabs', $this->_getEditTabs() );
		$this->view->setVar( 'option_tabs', $this->_getEditOptionTabs() );
		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'payment_method_option', $payment_method_option );

		$this->output( 'edit/options/edit/main' );

	}


	public function options_edit_images_Action() : void
	{

		$payment_method = $this->payment_method;
		$payment_method_option = $this->payment_method_option;
		
		Admin_Managers::UI()->initBreadcrumb();
		
		Navigation_Breadcrumb::addURL(
			Tr::_( 'Edit payment method <b>%NAME%</b>', [ 'NAME' => $payment_method->getInternalName() ] ),
			Http_Request::currentURI([], ['action', 'option'])
		);
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit option <b>%OPTION%</b>', ['OPTION'=>$payment_method_option->getShopData()->getTitle()] ) );

		Application_Admin::handleUploadTooLarge();

		foreach(Shops::getList() as $shop) {
			$payment_method_option->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'Payment method option',
				object_id: $payment_method_option->getCode(),
				object_name: $payment_method_option->getTitle(),
				upload_event: 'payment_method_option_image_uploaded',
				delete_event: 'payment_method_option_image_deleted'
			);
		}

		$this->view->setVar( 'tabs', $this->_getEditTabs() );
		$this->view->setVar( 'option_tabs', $this->_getEditOptionTabs() );
		$this->view->setVar( 'payment_method', $payment_method );
		$this->view->setVar( 'payment_method_option', $payment_method_option );

		$this->output( 'edit/options/edit/images' );

	}


}