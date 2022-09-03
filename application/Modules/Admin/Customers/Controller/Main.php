<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetShopModule\Admin\Customers;

use JetShop\Customer as Customer;

use Jet\MVC_Controller_Router_AddEditDelete;
use Jet\MVC_Controller_Default;
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
	 * @var ?Customer
	 */
	protected ?Customer $customer = null;

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
					return (bool)($this->customer = Customer::get($id));
				},
				[
					'listing'=> Main::ACTION_GET_CUSTOMER,
					'view'   => Main::ACTION_GET_CUSTOMER,
					'add'    => Main::ACTION_ADD_CUSTOMER,
					'edit'   => Main::ACTION_UPDATE_CUSTOMER,
					'delete' => Main::ACTION_DELETE_CUSTOMER,
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
	public function edit_Action() : void
	{
		$this->view_Action();
	}

	/**
	 *
	 */
	public function view_Action() : void
	{
		$customer = $this->customer;

		$this->_setBreadcrumbNavigation(
			Tr::_( 'Customer detail <b>%ITEM_NAME%</b>', [ 'ITEM_NAME' => $customer->getEmail() ] )
		);

		$form = $customer->getEditForm();

		$form->setIsReadonly();

		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'customer', $customer );

		$this->output( 'edit' );

	}


}