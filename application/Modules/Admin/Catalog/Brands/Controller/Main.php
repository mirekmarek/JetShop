<?php
namespace JetShopModule\Admin\Catalog\Brands;


use Jet\Logger;
use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;

use Jet\MVC_Controller_Default;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetShop\Application_Admin;
use JetShop\Brand;

use JetShop\Shops;
use JetShopModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	protected ?Brand $brand = null;

	protected ?MVC_Controller_Router_AddEditDelete $router = null;

	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->brand = Brand::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET_BRAND,
					'view'   => Main::ACTION_GET_BRAND,
					'add'    => Main::ACTION_ADD_BRAND,
					'edit'   => Main::ACTION_UPDATE_BRAND,
					'delete' => Main::ACTION_DELETE_BRAND,
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Brand' ) );

		$brand = new Brand();


		$form = $brand->getAddForm();

		if( $brand->catchAddForm() ) {
			$brand->save();

			Logger::success(
				event: 'brand_created',
				event_message: 'Brand \''.$brand->getName().'\' ('.$brand->getId().') created',
				context_object_id: $brand->getId(),
				context_object_name: $brand->getName(),
				context_object_data: $brand
			);

			UI_messages::success(
				Tr::_( 'Brand <b>%NAME%</b> has been created', [ 'NAME' => $brand->getName() ] )
			);

			Http_Headers::reload( ['id'=>$brand->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'brand', $brand );

		$this->output( 'add' );

	}


	public function edit_Action() : void
	{
		$brand = $this->brand;

		Application_Admin::handleUploadTooLarge();


		foreach(Shops::getList() as $shop) {
			$brand->getShopData( $shop )->catchImageWidget(
				shop: $shop,
				entity_name: 'Sticker',
				object_id: $brand->getId(),
				object_name: $brand->getName(),
				upload_event: 'brand_image_uploaded',
				delete_event: 'brand_image_deleted'
			);
		}


		$this->_setBreadcrumbNavigation( Tr::_( 'Edit brand <b>%NAME%</b>', [ 'NAME' => $brand->getName() ] ) );



		$form = $brand->getEditForm();

		if( $brand->catchEditForm() ) {

			$brand->save();

			Logger::success(
				event: 'brand_updated',
				event_message: 'Brand \''.$brand->getName().'\' ('.$brand->getId().') updated',
				context_object_id: $brand->getId(),
				context_object_name: $brand->getName(),
				context_object_data: $brand
			);

			UI_messages::success(
				Tr::_( 'Brand <b>%NAME%</b> has been updated', [ 'NAME' => $brand->getName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'brand', $brand );

		$this->output( 'edit' );

	}

	public function view_Action() : void
	{
		$brand = $this->brand;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Brand detail <b>%NAME%</b>', [ 'NAME' => $brand->getName() ] )
		);

		$form = $brand->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'brand', $brand );

		$this->output( 'edit' );

	}

	public function delete_Action() : void
	{
		$brand = $this->brand;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete brand <b>%NAME%</b>', [ 'NAME' => $brand->getName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$brand->delete();

			Logger::success(
				event: 'brand_deleted',
				event_message: 'Brand \''.$brand->getName().'\' ('.$brand->getId().') deleted',
				context_object_id: $brand->getId(),
				context_object_name: $brand->getName(),
				context_object_data: $brand
			);

			UI_messages::info(
				Tr::_( 'Brand <b>%NAME%</b> has been deleted', [ 'NAME' => $brand->getName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'brand', $brand );

		$this->output( 'delete-confirm' );
	}

}