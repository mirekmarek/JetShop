<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Discounts\CodesDefinition;

use Jet\Logger;
use JetShop\Discounts_Code as DiscountsCode;

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
	 * @var ?DiscountsCode
	 */
	protected ?DiscountsCode $discounts_code = null;

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
					return (bool)($this->discounts_code = DiscountsCode::get($id));
				},
				[
					'listing'=> Main::ACTION_GET_DISCOUNTS_CODE,
					'view'   => Main::ACTION_GET_DISCOUNTS_CODE,
					'add'    => Main::ACTION_ADD_DISCOUNTS_CODE,
					'edit'   => Main::ACTION_UPDATE_DISCOUNTS_CODE,
					'delete' => Main::ACTION_DELETE_DISCOUNTS_CODE,
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
		$this->_setBreadcrumbNavigation( Tr::_( 'Create a new Discounts Code' ) );

		$discounts_code = new DiscountsCode();


		$form = $discounts_code->getAddForm();

		if( $discounts_code->catchAddForm() ) {
			$discounts_code->save();

			Logger::success(
				'discount_code_created',
				'Discount code '.$discounts_code->getCode().' ('.$discounts_code->getCode().') created',
				$discounts_code->getId(),
				$discounts_code->getCode(),
				$discounts_code
			);

			UI_messages::success(
				Tr::_( 'Discounts Code <b>%ITEM_NAME%</b> has been created', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
			);

			Http_Headers::reload( ['id'=>$discounts_code->getId()], ['action'] );
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function edit_Action() : void
	{
		$discounts_code = $this->discounts_code;

		$this->_setBreadcrumbNavigation( Tr::_( 'Edit discounts code <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $discounts_code->getCode() ] ) );

		$form = $discounts_code->getEditForm();

		if( $discounts_code->catchEditForm() ) {

			$discounts_code->save();

			Logger::success(
				'discount_code_updated',
				'Discount code '.$discounts_code->getCode().' ('.$discounts_code->getCode().') updated',
				$discounts_code->getId(),
				$discounts_code->getCode(),
				$discounts_code
			);

			UI_messages::success(
				Tr::_( 'Discounts Code <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
			);

			Http_Headers::reload();
		}

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$discounts_code = $this->discounts_code;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Discounts Code detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
		);

		$form = $discounts_code->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'edit' );

	}

	/**
	 *
	 */
	public function delete_Action() : void
	{
		$discounts_code = $this->discounts_code;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Delete discounts code  <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
		);

		if( Http_Request::POST()->getString( 'delete' )=='yes' ) {
			$discounts_code->delete();
			Logger::success(
				'discount_code_deleted',
				'Discount code '.$discounts_code->getCode().' ('.$discounts_code->getCode().') deleted',
				$discounts_code->getId(),
				$discounts_code->getCode(),
				$discounts_code
			);

			UI_messages::info(
				Tr::_( 'Discounts Code <b>%ITEM_NAME%</b> has been deleted', [ 'ITEM_NAME' => $discounts_code->getCode() ] )
			);

			Http_Headers::reload([], ['action', 'id']);
		}


		$this->view->setVar( 'discounts_code', $discounts_code );

		$this->output( 'delete-confirm' );
	}

}