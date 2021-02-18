<?php
namespace JetShopModule\Admin\Catalog\Stickers;


use Jet\UI;
use Jet\UI_dataGrid;
use Jet\UI_messages;


use Jet\Mvc;
use Jet\Mvc_Controller_Default;
use Jet\Mvc_Controller_Router_AddEditDelete;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetShop\Application_Admin;
use JetShop\Shops;
use JetShop\Sticker;
use JetShop\Sticker_ShopData;

use Jet\Application;
use JetShopModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{

	protected ?Sticker $sticker = null;

	protected ?Mvc_Controller_Router_AddEditDelete $router = null;

	public function getControllerRouter() : Mvc_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new Mvc_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->sticker = Sticker::get((int)$id));
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

		$this->view->setVar( 'filter_form', $listing->filter_getForm());
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

			$this->logAllowedAction( 'Sticker created', $sticker->getId(), $sticker->getName(), $sticker );

			UI_messages::success(
				Tr::_( 'Sticker <b>%NAME%</b> has been created', [ 'NAME' => $sticker->getName() ] )
			);

			Http_Headers::reload( ['id'=>$sticker->getId()], ['action'] );
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
			$shop_code = $shop->getCode();
			$shop_name = $shop->getName();
			$shop_data = $sticker->getShopData( $shop_code );

			foreach( Sticker_ShopData::getImageClasses() as $image_class=>$image_class_name ) {
				$shop_data->catchImageWidget(
					$image_class,
					function() use ($image_class, $sticker, $shop_code, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'category image '.$image_class.' uploaded', $sticker->getId().':'.$shop_code, $sticker->getName().' - '.$shop_name );

					},
					function() use ($image_class, $sticker, $shop_code, $shop_name, $shop_data) {
						$shop_data->save();

						$this->logAllowedAction( 'category image '.$image_class.' deleted', $sticker->getId().':'.$shop_code, $sticker->getName().' - '.$shop_name );
					}
				);

			}
		}


		$this->_setBreadcrumbNavigation( Tr::_( 'Edit sticker <b>%NAME%</b>', [ 'NAME' => $sticker->getName() ] ) );



		$form = $sticker->getEditForm();

		if( $sticker->catchEditForm() ) {

			$sticker->save();
			$this->logAllowedAction( 'Sticker updated', $sticker->getId(), $sticker->getName(), $sticker );

			UI_messages::success(
				Tr::_( 'Sticker <b>%NAME%</b> has been updated', [ 'NAME' => $sticker->getName() ] )
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
			Tr::_( 'Sticker detail <b>%NAME%</b>', [ 'NAME' => $sticker->getName() ] )
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
			Tr::_( 'Delete sticker <b>%NAME%</b>', [ 'NAME' => $sticker->getName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$sticker->delete();
			$this->logAllowedAction( 'Sticker deleted', $sticker->getId(), $sticker->getName(), $sticker );

			UI_messages::info(
				Tr::_( 'Sticker <b>%NAME%</b> has been deleted', [ 'NAME' => $sticker->getName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'sticker', $sticker );

		$this->output( 'delete-confirm' );
	}

}