<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\Payment\Methods;

use Jet\AJAX;
use Jet\Application_Modules;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Navigation_Breadcrumb;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_WithEShopData_Controller;
use JetApplication\Application_Admin;
use JetApplication\Payment_Method;
use JetApplication\Payment_Method_Module;

class Controller_Main extends Admin_EntityManager_WithEShopData_Controller
{
	protected ?PaymentMethod_Option $option = null;
	
	
	public function getTabs(): array
	{
		$_tabs = [
			'main'    => Tr::_( 'Main data' ),
			'images'  => Tr::_( 'Images' ),
		];
		
		if(!$this->option) {
			$_tabs['options'] = Tr::_( 'Options' );
		}
		
		return $_tabs;
	}
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_FreeLimit() );
		$this->listing_manager->setDefaultColumnsSchema([
			'id',
			'active_state',
			'internal_name',
			'internal_code',
			'internal_notes',
			'payment_free_limit'
		]);
	}
	
	protected function currentItemGetter() : void
	{
		parent::currentItemGetter();
		
		if( $this->current_item ) {
			$option_id = Http_Request::GET()->getInt( 'option_id' );
			if($option_id) {
				$this->option = $this->current_item->getOption( $option_id );
				$this->option?->setEditable(Main::getCurrentUserCanEdit());
			}
		}
	}
	
	protected function setupRouter( string $action, string $selected_tab ) : void
	{
		if(
			!$this->option &&
			$selected_tab!='options'
		) {
			parent::setupRouter( $action, $selected_tab );

			$this->router->addAction('get_payment_method_specification', Main::ACTION_GET)
				->setResolver(function() use ($action, $selected_tab) {
					return $action=='get_payment_method_specification';
				});
			
			return;
		}
		
		$this->router->addAction('edit_options', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && !$this->option && $selected_tab=='options' && !$action;
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['id'=>$id, 'page'=>'properties'], ['option_id', 'action'] );
			});
		
		$this->router->addAction('edit_options_sort', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->current_item && !$this->option && $selected_tab=='options' && $action=='sort_options';
			});
		
		
		$this->router->addAction('edit_option_main', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->option && $selected_tab=='main' && !$action;
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['option_id'=>$id, 'page'=>'main'], ['action'] );
			});
		$this->router->addAction('edit_option_images', Main::ACTION_GET)
			->setResolver(function() use ($action, $selected_tab) {
				return $this->option && $selected_tab=='images' && !$action;
			})
			->setURICreator(function( int $id ) {
				return Http_Request::currentURI( ['option_id'=>$id, 'page'=>'images'], ['action'] );
			});
	}
	
	
	public function edit_options_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Options') );
		/**
		 * @var PaymentMethod $method
		 */
		$method = $this->current_item;
		
		$new_option = new PaymentMethod_Option();
		
		if($new_option->catchAddForm()) {
			$method->addOption( $new_option );
			
			UI_messages::success(
				Tr::_( 'Option <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $new_option->getInternalName() ] )
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar('new_option', $new_option);
		
		
		$this->view->setVar( 'method', $method );
		$this->output( 'edit/options' );
	}
	
	public function edit_options_sort_Action() : void
	{
		/**
		 * @var PaymentMethod $method
		 */
		$method = $this->current_item;
		
		$sort = explode('|', Http_Request::POST()->getString('sort_order'));
		$method->sortOptions( $sort );
		$method->save();
		
		Http_Headers::reload(unset_GET_params: ['action']);
		
	}
	
	public function edit_option_main_Action() : void
	{
		$this->setBreadcrumbNavigation( Tr::_('Payment methods') );
		
		
		/**
		 * @var PaymentMethod $method
		 */
		$method = $this->current_item;
		$option = $this->option;
		
		$this->edit_main_handleActivation( $option );
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Options' ),
			Http_Request::currentURI(set_GET_params: ['page'=>'options'], unset_GET_params: ['option_id'])
		);
		
		Navigation_Breadcrumb::addURL( $option->getInternalName() );
		
		$form = $option->getEditForm();
		
		if( $option->catchEditForm() ) {
			
			$option->save();
			
			UI_messages::success(
				Tr::_( 'Option <b>%ITEM_NAME%</b> has been updated', [ 'ITEM_NAME' => $option->getInternalName() ] )
			);
			
			Http_Headers::reload();
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'method', $method );
		$this->view->setVar( 'option', $option );
		
		$this->output( 'edit/option/main' );
		
	}
	
	public function edit_option_images_Action() : void
	{
		Application_Admin::handleUploadTooLarge();
		
		/**
		 * @var PaymentMethod $method
		 */
		$method = $this->current_item;
		$option = $this->option;
		
		$this->setBreadcrumbNavigation(
			Tr::_( 'Options' ),
			Http_Request::currentURI(set_GET_params: ['page'=>'options'], unset_GET_params: ['option_id'])
		);
		
		Navigation_Breadcrumb::addURL( $option->getInternalName() .' - '.Tr::_('Images') );
		
		$option->handleImages();
		
		$this->view->setVar( 'method', $method );
		$this->view->setVar( 'option', $option );
		
		$this->output( 'edit/option/images' );
	}
	
	public function edit_main_Action(): void
	{
		$this->handleSetPrice();
		
		parent::edit_main_Action();
		
		$this->content->output(
			$this->view->render('edit/main/set-price')
		);
		
	}
	
	
	public function handleSetPrice() : void
	{
		/**
		 * @var Payment_Method $product
		 */
		$method = $this->current_item;
		
		if( ($set_price_form = $method->getSetPriceForm()) ) {
			$this->view->setVar('set_price_form', $set_price_form);
			if($set_price_form->catch()) {
				Http_Headers::reload();
			}
		}
		
	}
	
	public function get_payment_method_specification_Action() :  void
	{
		$module_name = Http_Request::GET()->getString('module', valid_values: array_keys(Payment_Method::getModulesScope()));
		
		$res = [];
		
		if($module_name) {
			/**
			 * @var Payment_Method_Module $module
			 */
			$module = Application_Modules::moduleInstance( $module_name );
			$res = $module->getPaymentMethodSpecificationList();
		}
		
		AJAX::commonResponse( $res );
	}
	
}