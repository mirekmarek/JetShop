<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\TransferBetweenWarehouses;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_SourceWh() );
		$this->listing_manager->addColumn( new Listing_Column_TargetWh() );
		$this->listing_manager->addColumn( new Listing_Column_SentDate() );
		$this->listing_manager->addColumn( new Listing_Column_ReceiptDate() );
		$this->listing_manager->addColumn( new Listing_Column_Notes() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		
		
		
		$this->listing_manager->addFilter( new Listing_Filter_SourceWh() );
		$this->listing_manager->addFilter( new Listing_Filter_TargetWh() );
		$this->listing_manager->addFilter( new Listing_Filter_SentDate() );
		$this->listing_manager->addFilter( new Listing_Filter_ReceiptDate() );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'number',
			Listing_Column_SourceWh::KEY,
			Listing_Column_TargetWh::KEY,
			'status',
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
	
	
	protected function edit_main_initPlugins() : void
	{
		if(Main::getCurrentUserCanEdit()) {
			Plugin::initPlugins( $this->view, $this->current_item );
			
			$this->getEditorManager()->setPlugins( Plugin::getPlugins() );
			
			Plugin::handlePlugins();
		}
	}
	
}