<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\StockVerification;


use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use JetApplication\Supplier;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\Admin_EntityManager_Controller;
use JetApplication\KindOfProduct;


class Controller_Main extends Admin_EntityManager_Controller
{
	
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Warehouse() );
		$this->listing_manager->addColumn( new Listing_Column_Date() );
		$this->listing_manager->addColumn( new Listing_Column_Criteria() );
		$this->listing_manager->addColumn( new Listing_Column_Notes() );
		$this->listing_manager->addColumn( new Listing_Column_Items() );
		
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Warehouse() );
		$this->listing_manager->addFilter( new Listing_Filter_Date() );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			'number',
			'status',
			Listing_Column_Warehouse::KEY,
			Listing_Column_Date::KEY,
			Listing_Column_Criteria::KEY,
			Listing_Column_Notes::KEY,
			Listing_Column_Items::KEY,
		]);
		
		$this->listing_manager->setSearchWhereCreator( function( string $search ): array {
			$q = [];
			$q['number'] = $search;
			
			return $q;
		} );
		
		$this->listing_manager->setDefaultSort('-number');
	}
	
	public function setupRouter( string $action, string $selected_tab ): void
	{
		parent::setupRouter( $action, $selected_tab );
	}
	
	
	public function add_Action() : void
	{
		$this->current_item = new WarehouseManagement_StockVerification();
		
		$this->setBreadcrumbNavigation( Tr::_('New stock verification') );
		
		$GET = Http_Request::GET();
		
		$warehouses = WarehouseManagement_Warehouse::getScope();
		
		if(!$warehouses) {
			return;
		}
		
		$warehouse_id = $GET->getString('warehouse', default_value:array_keys($warehouses)[0], valid_values: array_keys($warehouses) );
		$this->view->setVar('warehouse_id', $warehouse_id);

		
		
		$warehouse = WarehouseManagement_Warehouse::get( $warehouse_id );
		
		$GET = Http_Request::GET();
		
		$suppliers = array_keys(Supplier::getScope());
		$supplier_id = (int)$GET->getString('supplier', default_value: '', valid_values: $suppliers );
		$this->view->setVar('supplier_id', $supplier_id);
		
		$kinds_of_product = array_keys(KindOfProduct::getScope());
		$kind_of_product_id = (int)$GET->getString('kind', default_value: '', valid_values: $kinds_of_product );
		$this->view->setVar('kind_of_product_id', $kind_of_product_id);
		
		$sectors = $warehouse->getSectors();
		if(!$sectors) {
			$sectors = [''];
		}
		$sector = $GET->getString('sector', default_value: '', valid_values: $sectors);
		$this->view->setVar('sectors', $sectors);
		$this->view->setVar('sector', $sector);
		
		
		$racks = $warehouse->getRacks( $sector );
		if(!$racks) {
			$racks = [''];
		}
		$rack = $GET->getString('rack', default_value: '', valid_values: $racks);
		$this->view->setVar('racks', $racks);
		$this->view->setVar('rack', $rack);
		
		
		$positions = $warehouse->getPositions( $sector, $rack );
		if(!$positions) {
			$positions = [''];
		}
		$position = $GET->getString('position', default_value: '', valid_values: $positions );
		$this->view->setVar('positions', $positions);
		$this->view->setVar('position', $position);
		
		
		if(
			$warehouse_id
		) {
			$this->current_item->setCriteriaSupplierId( $supplier_id );
			$this->current_item->setCriteriaKindOfProductId( $kind_of_product_id );
			$this->current_item->setCriteriaSector( $sector );
			$this->current_item->setCriteriaRack( $rack );
			$this->current_item->setCriteriaPosition( $position );
			
			$this->current_item->prepareNew( $warehouse_id );
			
			
			if($this->current_item->catchAddForm()) {
				$this->current_item->save();
				
				Http_Headers::reload(
					set_GET_params: ['id'=>$this->current_item->getId()],
					unset_GET_params: ['action', 'warehouse', 'supplier', 'kind', 'sector', 'rack', 'position']
				);
			}
			
			$this->view->setVar('verification', $this->current_item);
		}
		

		$this->output('add');
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