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
use JetApplication\ExpectedPayment;
use JetApplication\ExpectedPayment_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'exptected_payment_event',
	database_table_name: 'exptected_payment_events',
)]
class Core_ExpectedPayment_Event extends EShopEntity_Event
{
	
	protected static string $handler_module_name_prefix = 'Events.ExpectedPayment.';
	
	protected static string $event_base_class_name = ExpectedPayment_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $exptected_payment_id = 0;
	
	protected ?ExpectedPayment $_exptected_payment = null;
	
	public function setExpectedPayment( ExpectedPayment $exptected_payment ) : static
	{
		$this->_exptected_payment = $exptected_payment;
		$this->exptected_payment_id = $exptected_payment->getId();
		
		return $this;
	}
	
	public function getExpectedPaymentId() : int
	{
		return $this->exptected_payment_id;
	}
	
	public function getExpectedPayment() : ExpectedPayment
	{
		if($this->_exptected_payment===null) {
			$this->_exptected_payment = ExpectedPayment::get($this->exptected_payment_id);
		}
		
		return $this->_exptected_payment;
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
				'exptected_payment_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
