<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Modules;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;

use JetApplication\EShopEntity_Event;
use JetApplication\ReturnOfGoods;
use JetApplication\ReturnOfGoods_Event_HandlerModule;
use JetApplication\ReturnOfGoods_event;

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
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $return_of_goods_id = 0;

	protected ?ReturnOfGoods $_return_of_goods = null;
	
	
	public static function getEventHandlerModule( string $event_name ) : ReturnOfGoods_Event_HandlerModule
	{
		/**
		 * @var ReturnOfGoods_Event $this
		 * @var ReturnOfGoods_Event_HandlerModule $module
		 */
		$module = Application_Modules::moduleInstance( static::getHandlerModuleNamePrefix().$event_name );
		
		return $module;
	}
	

	public function setReturnOfGoodsId( int $value ) : static
	{
		$this->return_of_goods_id = $value;

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
	
	public function getHandlerModule() : ?ReturnOfGoods_Event_HandlerModule
	{
		/**
		 * @var ReturnOfGoods_event $this
		 * @var ReturnOfGoods_Event_HandlerModule $module
		 */
		if(!Application_Modules::moduleIsActivated( $this->getHandlerModuleName() )) {
			return null;
		}
		
		$module = Application_Modules::moduleInstance( $this->getHandlerModuleName() );
		$module->init( $this );

		return $module;
	}

	public function handle() : bool
	{
		return $this->getHandlerModule()->handle();
	}

	public static function newEvent( ReturnOfGoods $return, string $event ) : ReturnOfGoods_Event
	{
		$e = new ReturnOfGoods_Event();
		$e->setEvent( $event );
		$e->setEshop( $return->getEshop() );
		$e->setReturnOfGoodsId( $return->getId() );
		$e->created_date_time = Data_DateTime::now();

		return $e;
	}
	
	
	
	/**
	 * @param int $return_of_goods_id
	 *
	 * @return static[]
	 */
	public static function getForReturnOfGoods( int $return_of_goods_id ) : array
	{
		return static::fetch(
			[''=>[
				'return_of_goods_id' => $return_of_goods_id
			]],
			order_by: ['-id']
		);
	}
	
}
