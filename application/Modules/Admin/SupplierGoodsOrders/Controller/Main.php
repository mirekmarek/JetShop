<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\SupplierGoodsOrders;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\Supplier;
use JetApplication\Supplier_GoodsOrder;

class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Supplier() );
		$this->listing_manager->addColumn( new Listing_Column_OrderCreatedDate() );
		$this->listing_manager->addColumn( new Listing_Column_Notes() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		$this->listing_manager->addColumn( new Listing_Column_NumberBySupplier() );
		
		$this->listing_manager->addFilter( new Listing_Filter_OrderCreatedDate() );
		$this->listing_manager->addFilter( new Listing_Filter_Supplier() );
		
		$this->listing_manager->setDefaultColumnsSchema( [
			Listing_Column_Supplier::KEY,
			'status',
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
		
		$this->router->addAction('set_supplier_order_number')->setResolver(function() use ($action) {
			return $this->current_item && $action=='set_supplier_order_number';
		});
		
	}
	
	public function edit_main_Action() : void
	{
		parent::edit_main_Action();
		
		$this->content->output(
			$this->content->getOutput().
			$this->view->render('edit/main/dialogs')
		);
		
	}
	
	public function set_supplier_order_number_Action() : void
	{
		/**
		 * @var Supplier_GoodsOrder $order
		 */
		$order = $this->current_item;
		
		if( $order->getSetSupplierOrderNumberForm()->catch() ) {
			$order->save();
			
			UI_messages::success(
				$this->generateText_edit_main_msg()
			);
			
			Http_Headers::reload( unset_GET_params: ['action'] );
		}
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
			$this->getEditorManager()->renderAdd( $form )
		);
	}

	
	protected function edit_main_initPlugins() : void
	{
		Plugin::initPlugins( $this->view, $this->current_item );
		$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
		
		if(Main::getCurrentUserCanEdit()) {
			
			Plugin::handlePlugins();
		}
	}
	
}