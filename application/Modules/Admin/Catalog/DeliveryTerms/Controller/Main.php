<?php
namespace JetShopModule\Admin\Catalog\DeliveryTerms;


use Jet\Mvc_Controller_Router_AddEditDelete;
use Jet\UI_messages;

use Jet\Mvc_Controller_Default;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\Navigation_Breadcrumb;

use JetShop\Application_Admin;
use JetShop\DeliveryTerm;

use JetShopModule\Admin\UI\Main as UI_module;

/**
 *
 */
class Controller_Main extends Mvc_Controller_Default
{

	protected ?Mvc_Controller_Router_AddEditDelete $router = null;

	protected ?DeliveryTerm $delivery_term = null;

	public function getControllerRouter() : Mvc_Controller_Router_AddEditDelete
	{
		if( !$this->router ) {
			$this->router = new Mvc_Controller_Router_AddEditDelete(
				$this,
				function($id) {
					return (bool)($this->delivery_term = DeliveryTerm::get((int)$id));
				},
				[
					'listing'=> Main::ACTION_GET_DELIVERY_TERM,
					'view'   => Main::ACTION_GET_DELIVERY_TERM,
					'add'    => Main::ACTION_ADD_DELIVERY_TERM,
					'edit'   => Main::ACTION_UPDATE_DELIVERY_TERM,
					'delete' => Main::ACTION_DELETE_DELIVERY_TERM,
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new delivery term' ) );

		$delivery_term = new DeliveryTerm();


		$form = $delivery_term->getAddForm();

		if( $delivery_term->catchAddForm() ) {
			$delivery_term->save();

			$this->logAllowedAction( 'Delivery term created', $delivery_term->getId(), $delivery_term->getName(), $delivery_term );

			UI_messages::success(
				Tr::_( 'Delivery term <b>%NAME%</b> has been created', [ 'NAME' => $delivery_term->getName() ] )
			);

			Http_Headers::reload( ['id'=>$delivery_term->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_term', $delivery_term );

		$this->output( 'add' );

	}

	public function edit_Action() : void
	{
		$delivery_term = $this->delivery_term;

		Application_Admin::handleUploadTooLarge();

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit delivery term <b>%NAME%</b>', [ 'NAME' => $delivery_term->getName() ] ) );

		$form = $delivery_term->getEditForm();

		if( $delivery_term->catchEditForm() ) {

			$delivery_term->save();
			$this->logAllowedAction( 'Delivery term updated', $delivery_term->getId(), $delivery_term->getName(), $delivery_term );

			UI_messages::success(
				Tr::_( 'Delivery term <b>%NAME%</b> has been updated', [ 'NAME' => $delivery_term->getName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_term', $delivery_term );

		$this->output( 'edit' );

	}

	public function view_Action() : void
	{
		$delivery_term = $this->delivery_term;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delivery term detail <b>%NAME%</b>', [ 'NAME' => $delivery_term->getName() ] )
		);

		$form = $delivery_term->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_term', $delivery_term );

		$this->output( 'edit' );

	}

	public function delete_Action() : void
	{
		$delivery_term = $this->delivery_term;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete delivery term <b>%NAME%</b>', [ 'NAME' => $delivery_term->getName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$delivery_term->delete();
			$this->logAllowedAction( 'Delivery term deleted', $delivery_term->getId(), $delivery_term->getName(), $delivery_term );

			UI_messages::info(
				Tr::_( 'Delivery term <b>%NAME%</b> has been deleted', [ 'NAME' => $delivery_term->getName() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'delivery_term', $delivery_term );

		$this->output( 'delete-confirm' );
	}

}