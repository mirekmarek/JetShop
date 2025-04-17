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
use JetApplication\WarehouseManagement_ReceiptOfGoods;
use JetApplication\WarehouseManagement_ReceiptOfGoods_Event;

#[DataModel_Definition(
	name: 'whm_receipt_of_goods_event',
	database_table_name: 'whm_receipt_of_goods_events',
)]
abstract class Core_WarehouseManagement_ReceiptOfGoods_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.WHMReceiptOfGoods.';
	
	protected static string $event_base_class_name = WarehouseManagement_ReceiptOfGoods_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $rcp_id = 0;
	
	protected ?WarehouseManagement_ReceiptOfGoods $_rcp = null;
	
	
	public function setRcp( WarehouseManagement_ReceiptOfGoods $rcp ) : static
	{
		$this->rcp_id = $rcp->getId();
		$this->_rcp = $rcp;
		
		return $this;
	}
	
	public function getRcpId() : int
	{
		return $this->rcp_id;
	}
	
	public function getRcp() : WarehouseManagement_ReceiptOfGoods
	{
		if($this->_rcp===null) {
			$this->_rcp = WarehouseManagement_ReceiptOfGoods::get($this->rcp_id);
		}
		
		return $this->_rcp;
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
				'rcp_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
