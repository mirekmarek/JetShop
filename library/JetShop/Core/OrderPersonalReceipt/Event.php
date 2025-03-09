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
use JetApplication\OrderPersonalReceipt;
use JetApplication\OrderPersonalReceipt_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'order_personal_receipt_event',
	database_table_name: 'order_personal_receipts_events',
)]
abstract class Core_OrderPersonalReceipt_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.OrderPersonalReceipt.';
	
	protected static string $event_base_class_name = OrderPersonalReceipt_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $order_personal_receipt_id = 0;
	
	protected ?OrderPersonalReceipt $_order_personal_receipt = null;

	public function setOrderPersonalReceipt( OrderPersonalReceipt $value ) : static
	{
		$this->_order_personal_receipt = $value;
		$this->order_personal_receipt_id = $value->getId();
		
		return $this;
	}
	
	public function getOrderPersonalReceiptId() : int
	{
		return $this->order_personal_receipt_id;
	}
	
	public function getOrderPersonalReceipt() : OrderPersonalReceipt
	{
		if($this->_order_personal_receipt===null) {
			$this->_order_personal_receipt = OrderPersonalReceipt::load($this->order_personal_receipt_id);
		}
		
		return $this->_order_personal_receipt;
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
				'order_personal_receipt_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
}
