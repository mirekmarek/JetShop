<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\Warehouses;

use Jet\Logger;
use JetApplication\Admin_Managers;
use JetApplication\WarehouseManagement_Warehouse as Warehouse;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\MVC_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

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
	 * @var ?Warehouse
	 */
	protected ?Warehouse $warehouse = null;

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
					return (bool)($this->warehouse = Warehouse::get($id));
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Warehouse' ) );

		$warehouse = new Warehouse();


		$form = $warehouse->getAddForm();

		if( $warehouse->catchAddForm() ) {
			$warehouse->save();

			Logger::success(
				'warehouse_created',
				'Warehouse '.$warehouse->getInternalName().' ('.$warehouse->getCode().') created',
				$warehouse->getCode(),
				$warehouse->getInternalName(),
				$warehouse
			);

			UI_messages::success(
				Tr::_( 'Warehouse <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $warehouse->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$warehouse->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'warehouse', $warehouse );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function edit_Action() : void
	{
		$warehouse = $this->warehouse;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit warehouse <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $warehouse->getInternalName() ] ) );

		$form = $warehouse->getEditForm();

		if( $warehouse->catchEditForm() ) {

			$warehouse->save();
			Logger::success(
				'warehouse_updated',
				'Warehouse '.$warehouse->getInternalName().' ('.$warehouse->getCode().') updated',
				$warehouse->getCode(),
				$warehouse->getInternalName(),
				$warehouse
			);

			UI_messages::success(
				Tr::_( 'Warehouse <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $warehouse->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'warehouse', $warehouse );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$warehouse = $this->warehouse;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Warehouse detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $warehouse->getInternalName() ] )
		);

		$form = $warehouse->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'warehouse', $warehouse );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$warehouse = $this->warehouse;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete warehouse  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $warehouse->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$warehouse->delete();

			Logger::success(
				'warehouse_deleted',
				'Warehouse '.$warehouse->getInternalName().' ('.$warehouse->getCode().') deleted',
				$warehouse->getCode(),
				$warehouse->getInternalName(),
				$warehouse
			);

			UI_messages::info(
				Tr::_( 'Warehouse <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $warehouse->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'warehouse', $warehouse );

		$this->output( 'delete-confirm' );
	}

}