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
use Jet\Factory_MVC;

use JetApplication\Admin_Managers;
use JetApplication\Application_Admin;

/**
 *
 */
class Controller_Main extends MVC_Controller_Default
{

	protected ?MVC_Controller_Router_AddEditDelete $router = null;

	protected ?Supplier $supplier = null;
	
	protected ?Listing $listing = null;
	
	
	public function getControllerRouter() : MVC_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new MVC_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					$this->supplier = Supplier::get((int)$id);
					$this->supplier?->setEditable(Main::getCurrentUserCanEdit());
					return (bool)($this->supplier);
				},
				[
					'listing'=> Main::ACTION_GET,
					'view'   => Main::ACTION_GET,
					'add'    => Main::ACTION_ADD,
					'edit'   => Main::ACTION_UPDATE,
					'delete' => Main::ACTION_DELETE,
				]
			);
		}

		return $this->router;
	}

	protected function _setBreadcrumbNavigation( string $current_label = '' ) : void
	{
		Admin_Managers::UI()->initBreadcrumb();

		if( $current_label ) {
			Navigation_Breadcrumb::addURL( $current_label );
		}
	}
	
	protected function getListing() : Listing
	{
		if(!$this->listing) {
			$column_view = Factory_MVC::getViewInstance( $this->view->getScriptsDir().'list/column/' );
			$column_view->setController( $this );
			$filter_view = Factory_MVC::getViewInstance( $this->view->getScriptsDir().'list/filter/' );
			$filter_view->setController( $this );
			
			$this->listing = new Listing(
				column_view: $column_view,
				filter_view: $filter_view
			);
		}
		
		return $this->listing;
	}
	
	/**
	 *
	 */
	public function listing_Action() : void
	{
		$listing = $this->getListing();
		$listing->handle();
		
		$this->view->setVar( 'listing', $listing );
		
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
				'Supplier '.$supplier->getInternalName().' ('.$supplier->getId().') created',
				$supplier->getId(),
				$supplier->getInternalName(),
				$supplier
			);

			UI_messages::success(
				Tr::_( 'Supplier <b>%NAME%</b> has been created', [ 'NAME' => $supplier->getInternalName() ] )
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


		$this->_setBreadcrumbNavigation( Tr::_( 'Edit supplier <b>%NAME%</b>', [ 'NAME' => $supplier->getInternalName() ] ) );



		$form = $supplier->getEditForm();

		if( $supplier->catchEditForm() ) {

			$supplier->save();
			Logger::success(
				'supplier_updated',
				'Supplier '.$supplier->getInternalName().' ('.$supplier->getId().') updated',
				$supplier->getId(),
				$supplier->getInternalName(),
				$supplier
			);

			UI_messages::success(
				Tr::_( 'Supplier <b>%NAME%</b> has been updated', [ 'NAME' => $supplier->getInternalName() ] )
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
			Tr::_( 'Supplier detail <b>%NAME%</b>', [ 'NAME' => $supplier->getInternalName() ] )
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
			Tr::_( 'Delete supplier <b>%NAME%</b>', [ 'NAME' => $supplier->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$supplier->delete();
			Logger::success(
				'supplier_deleted',
				'Supplier '.$supplier->getInternalName().' ('.$supplier->getId().') deleted',
				$supplier->getId(),
				$supplier->getInternalName(),
				$supplier
			);

			UI_messages::info(
				Tr::_( 'Supplier <b>%NAME%</b> has been deleted', [ 'NAME' => $supplier->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'supplier', $supplier );

		$this->output( 'delete-confirm' );
	}

}