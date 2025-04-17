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
use JetApplication\ProductQuestion;
use JetApplication\ProductQuestion_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'product_questions_events',
	database_table_name: 'product_questions_events',
)]
abstract class Core_ProductQuestion_Event extends EShopEntity_Event
{

	protected static string $handler_module_name_prefix = 'Events.ProductQuestion.';
	
	protected static string $event_base_class_name = ProductQuestion_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $review_id = 0;

	protected ?ProductQuestion $_review = null;
	
	
	public function setProductQuestion( ProductQuestion $review ) : static
	{
		$this->review_id = $review->getId();
		$this->_review = $review;

		return $this;
	}

	public function getProductQuestionId() : int
	{
		return $this->review_id;
	}

	public function getProductQuestion() : ProductQuestion
	{
		if($this->_review===null) {
			$this->_review = ProductQuestion::get($this->review_id);
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
