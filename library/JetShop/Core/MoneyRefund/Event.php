<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Modules;
use Jet\Auth;
use Jet\DataModel;
use Jet\DataModel_Definition;
use Jet\Data_DateTime;

use JetApplication\EShopEntity_Event;
use JetApplication\MoneyRefund_Event_HandlerModule;
use JetApplication\MoneyRefund;
use JetApplication\MoneyRefund_event;

/**
 *
 */
#[DataModel_Definition(
	name: 'money_refund_event',
	database_table_name: 'money_refunds_events',
)]
class Core_MoneyRefund_Event extends EShopEntity_Event
{

	protected static string $handler_module_name_prefix = 'Events.MoneyRefund.';
	
	#[DataModel_Definition(
		type: DataModel::TYPE_INT,
		is_key: true,
	)]
	protected int $money_refund_id = 0;

	protected ?MoneyRefund $_money_refund = null;
	
	
	public static function getEventHandlerModule( string $event_name ) : MoneyRefund_Event_HandlerModule
	{
		/**
		 * @var MoneyRefund_Event $this
		 * @var MoneyRefund_Event_HandlerModule $module
		 */
		$module = Application_Modules::moduleInstance( static::getHandlerModuleNamePrefix().$event_name );
		
		return $module;
	}

	public function setMoneyRefundId( int $value ) : static
	{
		$this->money_refund_id = $value;

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
	
	public function getHandlerModule() : ?MoneyRefund_Event_HandlerModule
	{
		
		/**
		 * @var MoneyRefund_event $this
		 * @var MoneyRefund_Event_HandlerModule $module
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

	public function handleImmediately() : bool
	{
		$this->handled_immediately = true;

		return $this->handle();
	}

	public static function newEvent( MoneyRefund $money_refund, string $event ) : MoneyRefund_Event
	{
		$e = new MoneyRefund_Event();
		$e->setEvent( $event );
		$e->setEshop( $money_refund->getEshop() );
		$e->setMoneyRefundId( $money_refund->getId() );
		$e->created_date_time = Data_DateTime::now();
		
		$admin = Auth::getCurrentUser();
		$e->setAdministrator( $admin->getName() );
		$e->setAdministratorId( $admin->getId() );

		return $e;
	}
	
	
	/**
	 * @param int $money_refund_id
	 *
	 * @return static[]
	 */
	public static function getForMoneyRefund( int $money_refund_id ) : array
	{
		return static::fetch(
			[''=>[
				'money_refund_id' => $money_refund_id
			]],
			order_by: ['-id']
		);
	}
	
}
