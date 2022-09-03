<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\System\OrderStatus;

use Jet\Logger;
use JetShop\Order_Status as OrderStatus;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\MVC_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetShopModule\Admin\UI\Main as UI_module;

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
	 * @var ?OrderStatus
	 */
	protected ?OrderStatus $order_status = null;

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
					return (bool)($this->order_status = OrderStatus::get($id));
				},
				[
					'listing'=> Main::ACTION_GET_ORDER_STATUS,
					'view'   => Main::ACTION_GET_ORDER_STATUS,
					'add'    => Main::ACTION_ADD_ORDER_STATUS,
					'edit'   => Main::ACTION_UPDATE_ORDER_STATUS,
					'delete' => Main::ACTION_DELETE_ORDER_STATUS,
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

		$this->view->setVar( 'filter_form', $listing->getFilterForm());
		$this->view->setVar( 'grid', $listing->getGrid() );

		$this->output( 'list' );
	}

	/**
	 *
	 */
	public function add_Action() : void
	{
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Order Status' ) );

		$order_status = new OrderStatus();


		$form = $order_status->getAddForm();

		if( $order_status->catchAddForm() ) {
			$order_status->save();

			Logger::success(
				'order_status_created',
				'Order status '.$order_status->getInternalName().' ('.$order_status->getCode().') created',
				$order_status->getCode(),
				$order_status->getInternalName(),
				$order_status
			);

			UI_messages::success(
				Tr::_( 'Order Status <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $order_status->getInternalName() ] )
			);

			Http_Headers::reload( ['id'=>$order_status->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'order_status', $order_status );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function edit_Action() : void
	{
		$order_status = $this->order_status;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit order status <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $order_status->getInternalName() ] ) );

		$form = $order_status->getEditForm();

		if( $order_status->catchEditForm() ) {

			$order_status->save();
			Logger::success(
				'order_status_updated',
				'Order status '.$order_status->getInternalName().' ('.$order_status->getCode().') updated',
				$order_status->getCode(),
				$order_status->getInternalName(),
				$order_status
			);

			UI_messages::success(
				Tr::_( 'Order Status <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $order_status->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'order_status', $order_status );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$order_status = $this->order_status;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Order Status detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $order_status->getInternalName() ] )
		);

		$form = $order_status->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'order_status', $order_status );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$order_status = $this->order_status;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete order status  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $order_status->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$order_status->delete();
			Logger::success(
				'order_status_deleted',
				'Order status '.$order_status->getInternalName().' ('.$order_status->getCode().') deleted',
				$order_status->getCode(),
				$order_status->getInternalName(),
				$order_status
			);

			UI_messages::info(
				Tr::_( 'Order Status <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $order_status->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'order_status', $order_status );

		$this->output( 'delete-confirm' );
	}

}