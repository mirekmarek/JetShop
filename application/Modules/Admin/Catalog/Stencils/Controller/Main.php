<?php
namespace JetShopModule\Admin\Catalog\Stencils;

use JetShop\Application_Admin;
use JetShop\Stencil;

use Jet\UI_messages;
use Jet\Mvc_Controller_Router_AddEditDelete;

use Jet\Mvc_Controller_Default;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use Jet\AJAX;
use JetShop\Shops;

use JetShop\Stencil_Option;
use JetShop\Stencil_Option_ShopData;
use JetShopModule\Admin\UI\Main as UI_module;

use Jet\Application;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{

	protected ?Mvc_Controller_Router_AddEditDelete $router = null;

	protected ?Stencil $stencil = null;

	protected ?Stencil_Option $option = null;

	public function getControllerRouter() : Mvc_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new Mvc_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->stencil = Stencil::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET_STENCIL,
					'view'   => Main::ACTION_GET_STENCIL,
					'add'    => Main::ACTION_ADD_STENCIL,
					'edit'   => Main::ACTION_UPDATE_STENCIL,
					'delete' => Main::ACTION_DELETE_STENCIL,
				]
			);

			$GET = Http_Request::GET();
			$action = $GET->getString('action');
			$id = $GET->getInt('id');
			$option_id = $GET->getInt('option_id');

			$this->router->addAction( 'create_option', Main::ACTION_UPDATE_STENCIL )
				->setResolver( function() use ($action, $id) {
					return (
						$action=='create_option' &&
						($this->stencil = Stencil::get($id))
					);
				} )
				->setURICreator( function( $id ) {
					return Http_Request::currentURI(['id'=>$id, 'action'=>'create_option']);
				} );

			$this->router->addAction( 'save_sort_options', Main::ACTION_UPDATE_STENCIL )
				->setResolver( function() use ($action, $id) {
					return (
						$action=='save_sort_options' &&
						($this->stencil = Stencil::get($id))
					);
				} )
				->setURICreator( function( $id ) {
					return Http_Request::currentURI(['id'=>$id, 'action'=>'save_sort_options']);
				} );

			$this->router->addAction( 'edit_option', Main::ACTION_UPDATE_STENCIL )
				->setResolver( function() use ($action, $id, $option_id) {
					return (
						$action=='edit_option' &&
						($this->stencil = Stencil::get($id)) &&
						($this->option = $this->stencil->getOption( $option_id ))
					);
				} )
				->setURICreator( function( $id, $option_id ) {
					return Http_Request::currentURI(['id'=>$id, 'action'=>'edit_option', 'option_id' => $option_id]);
				} );

			$this->router->addAction('generate_url_path_part')
				->setResolver( function() use ($action) {
					return (
						$action=='generate_url_path_part'
					);
				} );

		}

		return $this->router;
	}

	protected function _setBreadcrumbNavigation( $current_label = '' ) : void
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

	public function generate_url_path_part_Action() : void
	{
		$GET = Http_Request::GET();

		AJAX::response([
			'url_path_part' => Shops::generateURLPathPart( $GET->getString('name'), '', 0, $GET->getString('shop_id') )
		]);

		Application::end();
	}

	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Stencil' ) );

		$stencil = new Stencil();


		$form = $stencil->getAddForm();

		if( $stencil->catchAddForm() ) {
			$stencil->save();

			$this->logAllowedAction( 'Stencil created', $stencil->getId(), $stencil->getName(), $stencil );

			UI_messages::success(
				Tr::_( 'Stencil <b>%NAME%</b> has been created', [ 'NAME' => $stencil->getName() ] )
			);

			Http_Headers::reload( ['id'=>$stencil->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'stencil', $stencil );

		$this->output( 'edit' );

	}

	public function edit_Action() : void
	{
		$stencil = $this->stencil;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit stencil <b>%NAME%</b>', [ 'NAME' => $stencil->getName() ] ) );

		$form = $stencil->getEditForm();

		if( $stencil->catchEditForm() ) {

			$stencil->save();
			$this->logAllowedAction( 'Stencil updated', $stencil->getId(), $stencil->getName(), $stencil );

			UI_messages::success(
				Tr::_( 'Stencil <b>%NAME%</b> has been updated', [ 'NAME' => $stencil->getName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'create_option_url', Http_Request::currentURL(['action'=>'create_option']));
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'stencil', $stencil );

		$this->output( 'edit' );

	}

	public function save_sort_options_Action() : void
	{
		$stencil = $this->stencil;

		$ids = explode('|', Http_Request::POST()->getString('sort_order'));

		$priority = 0;
		foreach( $ids as $id ) {
			$option = $stencil->getOption($id);
			if(!$option) {
				continue;
			}
			$priority++;

			$option->setPriority( $priority );
			$option->save();
			$this->logAllowedAction( 'stencil.option priority updated', $option->getId(), $option->getShopData()->getFilterLabel(), $priority );
		}

		Http_Headers::reload([], ['action']);
	}

	public function create_option_Action() : void
	{
		$stencil = $this->stencil;

		UI_module::initBreadcrumb();
		Navigation_Breadcrumb::addURL(
			Tr::_( 'Edit stencil <b>%NAME%</b>', [ 'NAME' => $stencil->getName() ] ),
			Http_Request::currentURI([], ['action', 'option_id'])
		);
		Navigation_Breadcrumb::addURL( Tr::_( 'Create new option' ) );


		$new_option = new Stencil_Option();
		$new_option->setParents( $stencil );

		if( $new_option->catchAddForm() ) {
			$stencil->addOption( $new_option );
			$stencil->save();

			$this->logAllowedAction( 'Stencil updated - option created', $stencil->getId(), $stencil->getName(), $stencil );

			UI_messages::success(
				Tr::_( 'Stencil <b>%NAME%</b> has been updated - option created', ['NAME' => $stencil->getName()] )
			);

			Http_Headers::movedTemporary( Http_Request::currentURI( [], ['action'] ) );

		}

		$this->view->setVar( 'stencil', $stencil );
		$this->view->setVar( 'new_option', $new_option );

		$this->output( 'option/add' );
	}

	public function edit_option_Action() : void
	{
		$stencil = $this->stencil;
		$option = $this->option;

		UI_module::initBreadcrumb();
		Navigation_Breadcrumb::addURL(
			Tr::_( 'Edit stencil <b>%NAME%</b>', [ 'NAME' => $stencil->getName() ] ),
			Http_Request::currentURI([], ['action', 'option_id'])
		);
		Navigation_Breadcrumb::addURL( Tr::_( 'Edit option <b>%OPTION%</b>', ['OPTION'=>$option->getShopData()->getFilterLabel()] ) );

		Application_Admin::handleUploadTooLarge();

		if($option->catchEditForm()) {
			$stencil->save();

			$this->logAllowedAction( 'Stencil updated - option updated', $stencil->getId(), $stencil->getName(), $stencil );

			UI_messages::success(
				Tr::_( 'Stencil <b>%NAME%</b> has been updated - option updated', [ 'NAME' => $stencil->getName() ] )
			);

			Http_Headers::movedTemporary( Http_Request::currentURI() );

		}


		foreach(Shops::getList() as $shop) {
			$shop_id = $shop->getId();
			$shop_name = $shop->getName();
			$shop_data = $option->getShopData( $shop_id );

			foreach( Stencil_Option_ShopData::getImageClasses() as $image_class=>$image_class_name ) {
				$shop_data->catchImageWidget(
					$image_class,
					function() use ($image_class, $option, $shop_id, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'Stencil option image '.$image_class.' uploaded', $option->getId().':'.$shop_id, $shop_data->getFilterLabel().' - '.$shop_name );

					},
					function() use ($image_class, $option, $shop_id, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'Stencil option image '.$image_class.' deleted', $option->getId().':'.$shop_id, $shop_data->getFilterLabel().' - '.$shop_name );
					}
				);

			}
		}


		$this->view->setVar( 'stencil', $stencil );
		$this->view->setVar( 'option', $option );

		$this->output( 'option/edit' );
	}

	public function view_Action() : void
	{
		$stencil = $this->stencil;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Stencil detail <b>%NAME%</b>', [ 'NAME' => $stencil->getName() ] )
		);

		$form = $stencil->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'stencil', $stencil );

		$this->output( 'edit' );

	}

	public function delete_Action() : void
	{
		$stencil = $this->stencil;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete stencil <b>%NAME%</b>', [ 'NAME' => $stencil->getName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$stencil->delete();
			$this->logAllowedAction( 'Stencil deleted', $stencil->getId(), $stencil->getName(), $stencil );

			UI_messages::info(
				Tr::_( 'Stencil <b>%NAME%</b> has been deleted', [ 'NAME' => $stencil->getName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'stencil', $stencil );

		$this->output( 'delete-confirm' );
	}

}