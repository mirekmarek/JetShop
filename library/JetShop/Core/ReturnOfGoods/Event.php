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
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event;

/**
 *
 */
#[DataModel_Definition(
	name: 'return_of_goods_event',
	database_table_name: 'returns_of_goods_events',
)]
class Core_ReturnOfGoods_Event extends EShopEntity_Event
{

	protected static string $handler_module_name_prefix = 'Events.ReturnOfGoods.';
	
	protected static string $event_base_class_name = ReturnOfGoods_Event::class;
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $return_of_goods_id = 0;

	protected ?ReturnOfGoods $_return_of_goods = null;
	
	public function setReturnOfGoods( ReturnOfGoods $return_of_goods ) : static
	{
		$this->_return_of_goods = $return_of_goods;
		$this->return_of_goods_id = $return_of_goods->getId();

		return $this;
	}

	public function getReturnOfGoodsId() : int
	{
		return $this->return_of_goods_id;
	}

	public function getReturnOfGoods() : ReturnOfGoods
	{
		if($this->_return_of_goods===null) {
			$this->_return_of_goods = ReturnOfGoods::get($this->return_of_goods_id);
		}

		return $this->_return_of_goods;
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
				'return_of_goods_id' => $entity_id
			]],
			order_by: ['-id']
		);
	}
	
}
