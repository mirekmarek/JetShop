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
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'money_refund_event',
	database_table_name: 'money_refunds_events',
)]
abstract class Core_MoneyRefund_Event extends EShopEntity_Event
{

	protected static string $handler_module_name_prefix = 'Events.MoneyRefund.';
	
	protected static string $event_base_class_name = MoneyRefund_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $money_refund_id = 0;

	protected ?MoneyRefund $_money_refund = null;
	
	
	public function setMoneyRefund( MoneyRefund $money_refund ) : static
	{
		$this->money_refund_id = $money_refund->getId();
		$this->_money_refund = $money_refund;

		return $this;
	}

	public function getMoneyRefundId() : int
	{
		return $this->money_refund_id;
	}

	public function getMoneyRefund() : MoneyRefund
	{
		if($this->_money_refund===null) {
			$this->_money_refund = MoneyRefund::get($this->money_refund_id);
		}

		return $this->_money_refund;
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
				'money_refund_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
