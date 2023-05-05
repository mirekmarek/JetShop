<?php
namespace JetApplicationModule\Admin\Catalog\Suppliers;


use Jet\Logger;
use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;


use Jet\MVC_Controller_Default;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetApplication\Application_Admin;
use JetApplication\Supplier;

use JetApplicationModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	protected ?MVC_Controller_Router_AddEditDelete $router = null;

	protected ?Supplier $supplier = null;

	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->supplier = Supplier::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET_SUPPLIER,
					'view'   => Main::ACTION_GET_SUPPLIER,
					'add'    => Main::ACTION_ADD_SUPPLIER,
					'edit'   => Main::ACTION_UPDATE_SUPPLIER,
					'delete' => Main::ACTION_DELETE_SUPPLIER,
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new supplier' ) );

		$supplier = new Supplier();

		$form = $supplier->getAddForm();

		if( $supplier->catchAddForm() ) {
			$supplier->save();

			Logger::success(
				'supplier_created',
				'Supplier '.$supplier->getName().' ('.$supplier->getId().') created',
				$supplier->getId(),
				$supplier->getName(),
				$supplier
			);

			UI_messages::success(
				Tr::_( 'Supplier <b>%NAME%</b> has been created', [ 'NAME' => $supplier->getName() ] )
			);

			Http_Headers::reload( ['id'=>$supplier->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'supplier', $supplier );

		$this->output( 'add' );

	}

	public function edit_Action() : void
	{
		$supplier = $this->supplier;

		Application_Admin::handleUploadTooLarge();


		$this->_setBreadcrumbNavigation( Tr::_( 'Edit supplier <b>%NAME%</b>', [ 'NAME' => $supplier->getName() ] ) );



		$form = $supplier->getEditForm();

		if( $supplier->catchEditForm() ) {

			$supplier->save();
			Logger::success(
				'supplier_updated',
				'Supplier '.$supplier->getName().' ('.$supplier->getId().') updated',
				$supplier->getId(),
				$supplier->getName(),
				$supplier
			);

			UI_messages::success(
				Tr::_( 'Supplier <b>%NAME%</b> has been updated', [ 'NAME' => $supplier->getName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'supplier', $supplier );

		$this->output( 'edit' );

	}

	public function view_Action() : void
	{
		$supplier = $this->supplier;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Supplier detail <b>%NAME%</b>', [ 'NAME' => $supplier->getName() ] )
		);

		$form = $supplier->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'supplier', $supplier );

		$this->output( 'edit' );

	}

	public function delete_Action() : void
	{
		$supplier = $this->supplier;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete supplier <b>%NAME%</b>', [ 'NAME' => $supplier->getName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$supplier->delete();
			Logger::success(
				'supplier_deleted',
				'Supplier '.$supplier->getName().' ('.$supplier->getId().') deleted',
				$supplier->getId(),
				$supplier->getName(),
				$supplier
			);

			UI_messages::info(
				Tr::_( 'Supplier <b>%NAME%</b> has been deleted', [ 'NAME' => $supplier->getName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'supplier', $supplier );

		$this->output( 'delete-confirm' );
	}

}