<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\Admin\WarehouseManagement\LossOrDestruction;


use Jet\Data_DateTime;
use Jet\Http_Headers;
use Jet\Http_Request;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\WarehouseManagement_LossOrDestruction;
use JetApplication\WarehouseManagement_Warehouse;
use JetApplication\Admin_EntityManager_Controller;


class Controller_Main extends Admin_EntityManager_Controller
{
	public function setupListing(): void
	{
		$this->listing_manager->addColumn( new Listing_Column_Number() );
		$this->listing_manager->addColumn( new Listing_Column_Warehouse() );
		$this->listing_manager->addColumn( new Listing_Column_Status() );
		$this->listing_manager->addColumn( new Listing_Column_Date() );
		$this->listing_manager->addColumn( new Listing_Column_Product() );
		$this->listing_manager->addColumn( new Listing_Column_PricePerUnit() );
		$this->listing_manager->addColumn( new Listing_Column_NumberOfUnits() );
		$this->listing_manager->addColumn( new Listing_Column_Total() );
		$this->listing_manager->addColumn( new Listing_Column_Notes() );
		
		
		
		
		$this->listing_manager->addFilter( new Listing_Filter_Warehouse() );
		$this->listing_manager->addFilter( new Listing_Filter_Status() );
		$this->listing_manager->addFilter( new Listing_Filter_Date() );
		
		
		$this->listing_manager->setDefaultColumnsSchema([
			Listing_Column_Warehouse::KEY,
			Listing_Column_Number::KEY,
			Listing_Column_Date::KEY,
			Listing_Column_Status::KEY,
			Listing_Column_Product::KEY,
			//Listing_Column_PricePerUnit::KEY,
			Listing_Column_NumberOfUnits::KEY,
			Listing_Column_Total::KEY,
			Listing_Column_Notes::KEY,
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
		$this->current_item = new WarehouseManagement_LossOrDestruction();
		
		$this->setBreadcrumbNavigation( Tr::_('New loss or destruction') );
		
		$GET = Http_Request::GET();
		
		$warehouses = WarehouseManagement_Warehouse::getScope();
		
		if(!$warehouses) {
			return;
		}
		
		$warehouse_id = $GET->getString('warehouse', default_value:array_keys($warehouses)[0], valid_values: array_keys($warehouses) );
		
		$this->current_item->setDate( Data_DateTime::now() );
		$this->current_item->setWarehouseId( $warehouse_id );
		
		if($this->current_item->catchAddForm()) {
			$this->current_item->save();
			
			Http_Headers::reload(
				set_GET_params: ['id'=>$this->current_item->getId()],
				unset_GET_params: ['action']
			);
		}
		
		
		$this->view->setVar('warehouse_id', $warehouse_id);
		
		$this->view->setVar('rcp', $this->current_item);
		

		$this->output('add');
	}
	
	public function done_Action() : void
	{
		/**
		 * @var WarehouseManagement_LossOrDestruction $item
		 */
		$item = $this->current_item;
		
		if($item->done()) {
			UI_messages::success( Tr::_('Receipt of goods <b>%number%</b> has been completed', ['number'=>$item->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
	public function cancel_Action() : void
	{
		/**
		 * @var WarehouseManagement_LossOrDestruction $item
		 */
		$item = $this->current_item;
		
		if($item->cancel()) {
			UI_messages::success( Tr::_('Receipt of goods <b>%number%</b> has been cancelled', ['number'=>$item->getNumber()]) );
		}
		
		Http_Headers::reload(unset_GET_params: ['action']);
	}
	
}