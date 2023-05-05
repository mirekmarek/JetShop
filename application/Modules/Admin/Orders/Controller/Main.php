<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Orders;

use Jet\Logger;
use JetApplication\Order as Order;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\UI_messages;
use Jet\MVC_Controller_Default;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetApplicationModule\Admin\UI\Main as UI_module;

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
	 * @var ?Order
	 */
	protected ?Order $order = null;

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
					return (bool)($this->order = Order::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET_ORDER,
					'view'   => Main::ACTION_GET_ORDER,
					'edit'   => Main::ACTION_UPDATE_ORDER,
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
	}

	/**
	 *
	 */
	public function edit_Action() : void
	{
		$order = $this->order;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit order <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $order->getId() ] ) );

		/*
		$form = $order->getEditForm();

		if( $order->catchEditForm() ) {

			$order->save();

			Logger::success(
				'order_updated',
				'Order '.$order->getId().' updated',
				$order->getId(),
				$order->getId(),
				$order
			);

			UI_messages::success(
				Tr::_( 'Order <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $order->getId() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		*/
		$this->view->setVar( 'order', $order );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$order = $this->order;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Order detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $order->getId() ] )
		);

		/*
		$form = $order->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'order', $order );
		*/

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$order = $this->order;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete order  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $order->getId() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$order->delete();
			Logger::success(
				'order_deleted',
				'Order '.$order->getId().' deleted',
				$order->getId(),
				$order->getId(),
				$order
			);

			UI_messages::info(
				Tr::_( 'Order <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $order->getId() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'order', $order );

		$this->output( 'delete-confirm' );
	}

}