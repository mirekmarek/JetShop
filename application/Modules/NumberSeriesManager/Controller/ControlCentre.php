<?php
/**
 *
 * @copyright
 * @license
 * @author
 */
namespace JetApplicationModule\NumberSeriesManager;

use Jet\Http_Headers;
use Jet\Logger;
use Jet\Tr;
use Jet\UI_messages;
use JetApplication\Admin_ControlCentre_Module_Controller;
use JetApplication\DeliveryNote;
use JetApplication\Invoice;
use JetApplication\InvoiceInAdvance;
use JetApplication\Order;
use JetApplication\OrderDispatch;
use JetApplication\Shops;
use JetApplication\Supplier_GoodsOrder;
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_StockVerification;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplicationModule\Admin\Complaints\Complaint;
use JetApplicationModule\Admin\ReturnsOfGoods\ReturnOfGoods;
use Error;

/**
 *
 */
class Controller_ControlCentre extends Admin_ControlCentre_Module_Controller
{
	
	public function default_Action() : void
	{
		
		$entities = [];
		
		$entities[] = [
			'entity' => Order::getEntityType(),
			'per_shop' => true,
			'title' => Tr::_('Orders')
		];
		
		$entities[] = [
			'entity' => Invoice::getEntityType(),
			'per_shop' => true,
			'title' => Tr::_('Invoices')
		];
		
		$entities[] = [
			'entity' => InvoiceInAdvance::getEntityType(),
			'per_shop' => true,
			'title' => Tr::_('Invoices in advance')
		];
		
		$entities[] = [
			'entity' => DeliveryNote::getEntityType(),
			'per_shop' => true,
			'title' => Tr::_('Delivery note')
		];
		
		$entities[] = [
			'entity' => Complaint::getEntityType(),
			'per_shop' => true,
			'title' => Tr::_('Complaints')
		];
		
		$entities[] = [
			'entity' => ReturnOfGoods::getEntityType(),
			'per_shop' => true,
			'title' => Tr::_('Return of goods')
		];
		
		$entities[] = [
			'entity' => Supplier_GoodsOrder::getEntityType(),
			'per_shop' => false,
			'title' => Tr::_('Orders of goods from suppliers')
		];
		
		$entities[] = [
			'entity' => OrderDispatch::getEntityType(),
			'per_shop' => false,
			'title' => Tr::_('Order dispatch')
		];
		
		
		$entities[] = [
			'entity' => WarehouseManagement_ReceiptOfGoods::getEntityType(),
			'per_shop' => false,
			'title' => Tr::_('Warehouse Management - Receipt of goods')
		];
		
		$entities[] = [
			'entity' => WarehouseManagement_TransferBetweenWarehouses::getEntityType(),
			'per_shop' => false,
			'title' => Tr::_('Warehouse Management - Transfer Between Warehouses')
		];
		
		$entities[] = [
			'entity' => WarehouseManagement_StockVerification::getEntityType(),
			'per_shop' => false,
			'title' => Tr::_('Warehouse Management - Stock Verification')
		];
		
		
		
		$forms = [];
		
		$save = function( EntityConfig $config, $c_id ) {
			$ok = true;
			
			try {
				$config->saveConfigFile();
			} catch(Error $e) {
				$ok = false;
				UI_messages::danger( Tr::_('Error during configuration saving: ').$e->getMessage(), context: 'CC' );
			}
			
			if($ok) {
				Logger::info(
					event: 'number_series_config_updated',
					event_message: 'Number series configuration updated',
					context_object_id: $c_id,
					context_object_data: $config
				);
				UI_messages::success( Tr::_('Configuration has been saved'), context: 'CC' );
			}
			
			Http_Headers::reload();
		};
		
		foreach( $entities as $entity ) {
			$e = $entity['entity'];
			
			if($entity['per_shop']) {
				foreach( Shops::getList() as $shop ) {
					$config = new EntityConfig( $e, $shop );
					
					$forms[$e.'_'.$shop->getKey()] = $config->createForm( $e.'_'.$shop->getKey() );
					
					if($forms[$e.'_'.$shop->getKey()]->catch()) {
						$save( $config, $e.'_'.$shop->getKey() );
					}
				}
				
				continue;
			}
			
			$config = new EntityConfig( $e );
			
			$forms[$e] = $config->createForm( $e );
			
			if($forms[$e]->catch()) {
				$save( $config, $e );
			}
			
		}
		
		$this->view->setVar('entities', $entities);
		$this->view->setVar('forms', $forms);
		
		$this->output('control-centre/default');

	}
}