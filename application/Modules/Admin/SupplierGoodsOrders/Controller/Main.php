<?php
namespace JetApplicationModule\Admin\SupplierGoodsOrders;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Simple_Controller;
use JetApplication\Supplier;
use JetApplication\Supplier_GoodsOrder;

class Controller_Main extends Admin_EntityManager_Simple_Controller
{
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Supplier() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		$this->listing_manager->addColumn( new Listing_Column_OrderCreatedDate() );
		$this->listing_manager->addColumn( new Listing_Column_Notes() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		$this->listing_manager->addColumn( new Listing_Column_NumberBySupplier() );
		
		$this->listing_manager->addFilter( new Listing_Filter_OrderCreatedDate() );
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_Supplier() );
		
		$this->listing_manager->setDefaultColumnsSchema( [
			Listing_Column_Number::KEY,
			Listing_Column_Supplier::KEY,
			Listing_Column_Status::KEY,
			Listing_Column_OrderCreatedDate::KEY,
			Listing_Column_Notes::KEY,
			Listing_Column_Items::KEY,
			Listing_Column_NumberBySupplier::KEY,
		] );
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ): array {
			$q = [];
			$q['number'] = $search;
			$q[] = 'OR';
			$q['number_by_supplier'] = $search;
			
			return $q;
		} );
	}
	
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('cancel')->setResolver(function() use ($action) {
			return $this->current_item && $action=='cancel';
		});
		
		$this->router->addAction('send')->setResolver(function() use ($action) {
			return $this->current_item && $action=='send';
		});
	}

	
	public function add_Action() : void
	{
		$suppliers = array_keys(Supplier::getScope());
		$default_supplier_id = $suppliers[0];
		$supplier_id = (int)Http_Request::GET()->getString('supplier', default_value: $default_supplier_id, valid_values: $suppliers );
		
		$this->current_item = Supplier_GoodsOrder::prepareNew( Supplier::get($supplier_id) );
		
		$this->setBreadcrumbNavigation( Tr::_('Create new order') );
		
		
		$form = $this->current_item->getAddForm();
		
		if( $this->current_item->catchAddForm() ) {
			$this->current_item->save();
			
			
			UI_messages::success( $this->generateText_add_msg() );
			
			Http_Headers::reload(
				set_GET_params: ['id'=>$this->current_item->getId()],
				unset_GET_params: ['action','supplier']
			);
		}
		
		$this->view->setVar( 'form', $form );
		$this->view->setVar( 'item', $this->current_item );
		
		$this->content->output(
			$this->getEditorManager()->renderAdd(
				common_data_fields_renderer: function() {
					echo $this->view->render('add/common-form-fields');
				}
			)
		);
	}
	
	public function edit_main_handleActivation() : void
	{
		/**
		 * @var Supplier_GoodsOrder $order
		 */
		$order = $this->current_item;
		
		if( $order->getSetSupplierOrderNumberForm()->catch() ) {
			$order->save();
			Http_Headers::reload();
		}
		
	}
	
	public function send_Action() : void
	{
		/**
		 * @var Supplier_GoodsOrder $order
		 */
		$order = $this->current_item;
		
		if($order->send()) {
			UI_messages::success( Tr::_('Order <b>%number%</b> has been sent to the supplier', ['number'=>$order->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	public function cancel_Action() : void
	{
		/**
		 * @var Supplier_GoodsOrder $order
		 */
		$order = $this->current_item;
		
		if($order->cancel()) {
			UI_messages::success( Tr::_('Order <b>%number%</b> has been cancelled', ['number'=>$order->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
}