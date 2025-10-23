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
use JetApplication\InappropriateContentReporting;
use JetApplication\InappropriateContentReporting_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'product_review_event',
	database_table_name: 'product_reviews_events',
)]
abstract class Core_InappropriateContentReporting_Event extends EShopEntity_Event
{

	protected static string $handler_module_name_prefix = 'Events.InappropriateContentReporting.';
	
	protected static string $event_base_class_name = InappropriateContentReporting_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $review_id = 0;

	protected ?InappropriateContentReporting $_review = null;
	
	
	public function setInappropriateContentReporting( InappropriateContentReporting $review ) : static
	{
		$this->review_id = $review->getId();
		$this->_review = $review;

		return $this;
	}

	public function getInappropriateContentReportingId() : int
	{
		return $this->review_id;
	}

	public function getInappropriateContentReporting() : InappropriateContentReporting
	{
		if($this->_review===null) {
			$this->_review = InappropriateContentReporting::get($this->review_id);
		}

		return $this->_review;
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
				'review_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
