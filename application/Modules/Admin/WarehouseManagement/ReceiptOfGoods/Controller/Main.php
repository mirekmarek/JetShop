<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\ReceiptOfGoods;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Supplier;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\Admin_EntityManager_Simple_Controller;
use JetApplication\WarehouseManagement_ReceiptOfGoods;

/**
 *
 */
class Controller_Main extends Admin_EntityManager_Simple_Controller
{
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		$this->listing_manager->addColumn( new Listing_Column_ReceiptDate() );
		$this->listing_manager->addColumn( new Listing_Column_Supplier() );
		$this->listing_manager->addColumn( new Listing_Column_Notes() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		$this->listing_manager->addColumn( new Listing_Column_SuppliersBill() );
		$this->listing_manager->addColumn( new Listing_Column_OrderNumber() );
		
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Supplier() );
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_ReceiptDate() );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			Listing_Column_Number::KEY,
			Listing_Column_Supplier::KEY,
			Listing_Column_Status::KEY,
			Listing_Column_ReceiptDate::KEY,
			Listing_Column_Notes::KEY,
			Listing_Column_Items::KEY,
			Listing_Column_OrderNumber::KEY,
			Listing_Column_SuppliersBill::KEY,
		]);
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ): array {
			$q = [];
			$q['number'] = $search;
			$q[] = 'OR';
			$q['suppliers_bill_number'] = $search;
			$q[] = 'OR';
			$q['order_number'] = $search;
			
			return $q;
		} );
		
		$this->listing_manager->setDefaultSort('-number');
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('cancel')->setResolver(function() use ($action) {
			return $this->current_item && $action=='cancel';
		});
		
		$this->router->addAction('done')->setResolver(function() use ($action) {
			return $this->current_item && $action=='done';
		});
	}
	
	
	public function add_Action() : void
	{
		$this->current_item = new WarehouseManagement_ReceiptOfGoods();
		
		$this->setBreadcrumbNavigation( Tr::_('New Receipt Of Goods') );
		
		$GET = Http_Request::GET();
		
		$warehouses = WarehouseManagement_Warehouse::getScope();
		
		if(!$warehouses) {
			return;
		}
		
		$warehouse_id = $GET->getString('warehouse', default_value:array_keys($warehouses)[0], valid_values: array_keys($warehouses) );
		
		
		$order_number = $GET->getString('order');
		$order = null;
		$supplier_id = null;
		$supplier = null;
		
		if($order_number) {
			$order = Supplier_GoodsOrder::getByNumber( $order_number );
			
			if(
				$order &&
				!$GET->exists('warehouse')
			) {
				Http_Headers::reload(set_GET_params: ['order'=>$order->getNumber(), 'warehouse'=>$order->getDestinationWarehouseId()]);
			}
		}
		
		$suppliers = array_keys(Supplier::getScope());
		
		if(!$order_number) {
			$default_supplier_id = $suppliers[0];
			$supplier_id = (int)Http_Request::GET()->getString('supplier', default_value: $default_supplier_id, valid_values: $suppliers );
			$supplier = Supplier::load($supplier_id);
		}
		
		
		$this->view->setVar('warehouse_id', $warehouse_id);
		$this->view->setVar('supplier_id', $supplier_id);
		$this->view->setVar('order_number', $order_number);
		$this->view->setVar('order', $order);
		
		if(
			$supplier ||
			$order
		) {
			
			if($order) {
				$this->current_item->prepareNewByOrder( $warehouse_id, $order );
			} else {
				$this->current_item->prepareNew( $warehouse_id, $supplier );
			}
			
			if($this->current_item->catchAddForm()) {
				$this->current_item->save();
				
				Http_Headers::reload(
					set_GET_params: ['id'=>$this->current_item->getId()],
					unset_GET_params: ['action']
				);
			}
			
			$this->view->setVar('rcp', $this->current_item);
		}
		

		$this->output('add');
	}
	
	public function done_Action() : void
	{
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $order
		 */
		$order = $this->current_item;
		
		if($order->done()) {
			UI_messages::success( Tr::_('Receipt of goods <b>%number%</b> has been completed', ['number'=>$order->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	public function cancel_Action() : void
	{
		/**
		 * @var WarehouseManagement_ReceiptOfGoods $order
		 */
		$order = $this->current_item;
		
		if($order->cancel()) {
			UI_messages::success( Tr::_('Receipt of goods <b>%number%</b> has been cancelled', ['number'=>$order->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
}