<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Delivery\Classes;

use Jet\Logger;
use JetApplication\Delivery_Class;

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
	 * @var ?Delivery_Class
	 */
	protected ?Delivery_Class $delivery_class = null;

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
					return (bool)($this->delivery_class = Delivery_Class::get($id));
				},
				[
					'listing'=> Main::ACTION_GET_DELIVERY_CLASS,
					'view'   => Main::ACTION_GET_DELIVERY_CLASS,
					'add'    => Main::ACTION_ADD_DELIVERY_CLASS,
					'edit'   => Main::ACTION_UPDATE_DELIVERY_CLASS,
					'delete' => Main::ACTION_DELETE_DELIVERY_CLASS,
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Delivery Class' ) );

		$delivery_class = new Delivery_Class();


		$form = $delivery_class->getAddForm();

		if( $delivery_class->catchAddForm() ) {
			$delivery_class->save();

			Logger::success(
				'delivery_class_created',
				'Delivery class '.$delivery_class->getInternalName().' ('.$delivery_class->getCode().') created',
				$delivery_class->getCode(),
				$delivery_class->getInternalName(),
				$delivery_class
			);

			UI_messages::success(
				Tr::_( 'Delivery Class <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $delivery_class->getInternalName() ] )
			);

			Http_Headers::reload( ['code'=>$delivery_class->getCode()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_class', $delivery_class );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function edit_Action() : void
	{
		$delivery_class = $this->delivery_class;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit delivery class <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_class->getInternalName() ] ) );

		$form = $delivery_class->getEditForm();

		if( $delivery_class->catchEditForm() ) {

			$delivery_class->save();
			Logger::success(
				'delivery_class_updated',
				'Delivery class '.$delivery_class->getInternalName().' ('.$delivery_class->getCode().') updated',
				$delivery_class->getCode(),
				$delivery_class->getInternalName(),
				$delivery_class
			);

			UI_messages::success(
				Tr::_( 'Delivery Class <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $delivery_class->getInternalName() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_class', $delivery_class );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$delivery_class = $this->delivery_class;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delivery Class detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_class->getInternalName() ] )
		);

		$form = $delivery_class->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'delivery_class', $delivery_class );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$delivery_class = $this->delivery_class;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete delivery class  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $delivery_class->getInternalName() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$delivery_class->delete();
			Logger::success(
				'delivery_class_deleted',
				'Delivery class '.$delivery_class->getInternalName().' ('.$delivery_class->getCode().') deleted',
				$delivery_class->getCode(),
				$delivery_class->getInternalName(),
				$delivery_class
			);

			UI_messages::info(
				Tr::_( 'Delivery Class <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $delivery_class->getInternalName() ] )
			);

			Http_Headers::reload([], ['action', 'code']);
		}


		$this->view->setVar( 'delivery_class', $delivery_class );

		$this->output( 'delete-confirm' );
	}

}