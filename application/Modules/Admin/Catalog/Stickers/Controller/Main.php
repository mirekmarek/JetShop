<?php
namespace JetApplicationModule\Admin\Catalog\Stickers;


use Jet\Logger;
use Jet\UI_messages;

use Jet\MVC_Controller_Default;
use Jet\MVC_Controller_Router_AddEditDelete;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetApplication\Application_Admin;
use JetApplication\Shops;
use JetApplication\Sticker;

use JetApplicationModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	protected ?Sticker $sticker = null;

	protected ?MVC_Controller_Router_AddEditDelete $router = null;

	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($code) {
					return (bool)($this->sticker = Sticker::get((string)$code));
				},
				[
					'listing'=> Main::ACTION_GET_STICKER,
					'view'   => Main::ACTION_GET_STICKER,
					'add'    => Main::ACTION_ADD_STICKER,
					'edit'   => Main::ACTION_UPDATE_STICKER,
					'delete' => Main::ACTION_DELETE_STICKER,
				]
			);
		}

		return $this->router;
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

		$this->view->setVar( 'filter_form', $listing->getFilterForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

		$this->output( 'list' );
	}

	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Sticker' ) );

		$sticker = new Sticker();


		$form = $sticker->getAddForm();

		if( $sticker->catchAddForm() ) {
			$sticker->save();

			Logger::success(
				event: 'sticker_created',
				event_message: 'Brand \''.$sticker->getInternalName().'\' ('.$sticker->getCode().') created',
				context_object_id: $sticker->getCode(),
				context_object_name: $sticker->getInternalName(),
				context_object_data: $sticker
			);


			UI_messages::success(
				Tr::_( 'Sticker <b>%NAME%</b> has been created', [ 'NAME' => $sticker->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$sticker->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'sticker', $sticker );

		$this->output( 'add' );

	}

	public function edit_Action() : void
	{
		$sticker = $this->sticker;

		Application_Admin::handleUploadTooLarge();

		foreach(Shops::getList() as $shop) {
			$sticker->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'Sticker',
				object_id: $sticker->getCode(),
				object_name: $sticker->getInternalName(),
				upload_event: 'sticker_image_uploaded',
				delete_event: 'sticker_image_deleted'
			);
		}


		$this->_setBreadcrumbNavigation( Tr::_( 'Edit sticker <b>%NAME%</b>', [ 'NAME' => $sticker->getInternalName() ] ) );



		$form = $sticker->getEditForm();

		if( $sticker->catchEditForm() ) {

			$sticker->save();

			Logger::success(
				event: 'sticker_updated',
				event_message: 'Brand \''.$sticker->getInternalName().'\' ('.$sticker->getCode().') updated',
				context_object_id: $sticker->getCode(),
				context_object_name: $sticker->getInternalName(),
				context_object_data: $sticker
			);

			UI_messages::success(
				Tr::_( 'Sticker <b>%NAME%</b> has been updated', [ 'NAME' => $sticker->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'sticker', $sticker );

		$this->output( 'edit' );

	}

	public function view_Action() : void
	{
		$sticker = $this->sticker;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Sticker detail <b>%NAME%</b>', [ 'NAME' => $sticker->getInternalName() ] )
		);

		$form = $sticker->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'sticker', $sticker );

		$this->output( 'edit' );

	}

	public function delete_Action() : void
	{
		$sticker = $this->sticker;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete sticker <b>%NAME%</b>', [ 'NAME' => $sticker->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$sticker->delete();

			Logger::success(
				event: 'sticker_deleted',
				event_message: 'Brand \''.$sticker->getInternalName().'\' ('.$sticker->getCode().') deleted',
				context_object_id: $sticker->getCode(),
				context_object_name: $sticker->getInternalName(),
				context_object_data: $sticker
			);

			UI_messages::info(
				Tr::_( 'Sticker <b>%NAME%</b> has been deleted', [ 'NAME' => $sticker->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'sticker', $sticker );

		$this->output( 'delete-confirm' );
	}

}