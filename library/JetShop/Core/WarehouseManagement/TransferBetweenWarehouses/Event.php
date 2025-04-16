<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\DataModel;
use Jet\DataModel_Definition;

use JetApplication\EShopEntity_Event;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses;
use JetApplication\WarehouseManagement_TransferBetweenWarehouses_Event;

#[DataModel_Definition(
	name: 'whm_transfer_between_warehouses_event',
	database_table_name: 'whm_transfer_between_warehouses_events',
)]
abstract class Core_WarehouseManagement_TransferBetweenWarehouses_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.WHMTransferBetweenWarehouses.';
	
	protected static string $event_base_class_name = WarehouseManagement_TransferBetweenWarehouses_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $transfare_id = 0;
	
	protected ?WarehouseManagement_TransferBetweenWarehouses $_transfare = null;
	
	
	public function setTransfare( WarehouseManagement_TransferBetweenWarehouses $transfer ) : static
	{
		$this->transfare_id = $transfer->getId();
		$this->_transfare = $transfer;
		
		return $this;
	}
	
	public function getTransfareId() : int
	{
		return $this->transfare_id;
	}
	
	public function getTransfare() : WarehouseManagement_TransferBetweenWarehouses
	{
		if($this->_transfare===null) {
			$this->_transfare = WarehouseManagement_TransferBetweenWarehouses::get($this->transfare_id);
		}
		
		return $this->_transfare;
	}
	
	
	/**
	 * @param int $entity_id
	 *
	 * @return static[]
	 */
	public static function getEventsList( int $entity_id ) : array
	{
		return static::fetch(
			[''=>[
				'transfare_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
