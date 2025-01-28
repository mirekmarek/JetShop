<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;

use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;

/**
 *
 */
class Controller_Main extends Admin_EntityManager_Controller
{
	public function getEntityNameReadable() : string
	{
		return 'Warehouse Management - Transfer between warehouses';
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
		
		$this->router->addAction('cancel')->setResolver(function() use ($action) {
			return $this->current_item && $action=='cancel';
		});
		
		$this->router->addAction('sent')->setResolver(function() use ($action) {
			return $this->current_item && $action=='sent';
		});
		
		$this->router->addAction('received')->setResolver(function() use ($action) {
			return $this->current_item && $action=='received';
		});
		
	}

	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_SourceWh() );
		$this->listing_manager->addColumn( new Listing_Column_TargetWh() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		$this->listing_manager->addColumn( new Listing_Column_SentDate() );
		$this->listing_manager->addColumn( new Listing_Column_ReceiptDate() );
		$this->listing_manager->addColumn( new Listing_Column_Notes() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_SourceWh() );
		$this->listing_manager->addFilter( new Listing_Filter_TargetWh() );
		$this->listing_manager->addFilter( new Listing_Filter_SentDate() );
		$this->listing_manager->addFilter( new Listing_Filter_ReceiptDate() );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			Listing_Column_Number::KEY,
			Listing_Column_SourceWh::KEY,
			Listing_Column_TargetWh::KEY,
			Listing_Column_Status::KEY,
			Listing_Column_SentDate::KEY,
			Listing_Column_ReceiptDate::KEY,
			Listing_Column_Notes::KEY,
			Listing_Column_Items::KEY,
		]);
		
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ): array {
			$q = [];
			$q['number'] = $search;
			$q[] = 'OR';
			$q['suppliers_bill_number'] = $search;
			
			return $q;
		} );
		
		$this->listing_manager->setDefaultSort('-number');
		
	}

	
	public function add_Action() : void
	{
		$this->current_item = new WarehouseManagement_TransferBetweenWarehouses();
		
		$this->setBreadcrumbNavigation( Tr::_('New Transfare between warehouses') );
		
		$GET = Http_Request::GET();
		
		$warehouses = WarehouseManagement_Warehouse::getScope();
		
		if(!$warehouses) {
			return;
		}
		
		$source_warehouse_id = $GET->getString('source_warehouse', default_value:array_keys($warehouses)[0], valid_values: array_keys($warehouses) );
		$target_warehouse_id = $GET->getString('target_warehouse', default_value:array_keys($warehouses)[0], valid_values: array_keys($warehouses) );
		
		
		$this->view->setVar('source_warehouse_id', $source_warehouse_id);
		$this->view->setVar('target_warehouse_id', $target_warehouse_id);
		
		
		if(
			$source_warehouse_id &&
			$target_warehouse_id &&
			$source_warehouse_id!=$target_warehouse_id
		) {
			$this->current_item->setSourceWarehouseId( $source_warehouse_id );
			$this->current_item->setTargetWarehouseId( $target_warehouse_id );
			$this->current_item->prepareNew();
			
			$this->view->setVar('transfer', $this->current_item);
			
			if($this->current_item->catchAddForm()) {
				$this->current_item->save();
				
				Http_Headers::reload(
					set_GET_params: ['id'=>$this->current_item->getId()],
					unset_GET_params: ['action']
				);
			}
			
		}
		
		$this->output('add');
	}
	
	public function sent_Action() : void
	{
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $transfer
		 */
		$transfer = $this->current_item;
		
		if($transfer->sent()) {
			UI_messages::success( Tr::_('Transfer of goods <b>%number%</b> has been sent', ['number'=>$transfer->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	
	public function received_Action() : void
	{
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $transfer
		 */
		$transfer = $this->current_item;
		
		if($transfer->received()) {
			UI_messages::success( Tr::_('Transfer of goods <b>%number%</b> has been received', ['number'=>$transfer->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}

	
	public function cancel_Action() : void
	{
		/**
		 * @var WarehouseManagement_TransferBetweenWarehouses $transfer
		 */
		$transfer = $this->current_item;
		
		if($transfer->cancel()) {
			UI_messages::success( Tr::_('Transfer of goods <b>%number%</b> has been cancelled', ['number'=>$transfer->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
}