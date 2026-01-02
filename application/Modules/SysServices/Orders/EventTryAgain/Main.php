<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Orders\EventTryAgain;


use Jet\Application_Module;
use Jet\Data_DateTime;
use Jet\Tr;
use JetApplication\Order;
use JetApplication\Order_Event;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$service = new SysServices_Definition(
			module: $this,
			name: Tr::_('Orders - failed events - try again'),
			description: Tr::_('Try again to handle failed order events'),
			service_code: 'try_again',
			service: function() {
				$this->tryAgain();
			}
		);
		
		$service->setIsPeriodicallyTriggeredService( true );
		
		return [
			$service
		];
	}
	
	public function tryAgain() : void
	{
		$timeline = new Data_DateTime( date('Y-m-d H:i:s', strtotime('-1 hour')) );
		
		$events = Order_Event::fetch([''=>[
			'handled' => false,
			'AND',
			'handled_date_time < ' => $timeline
		]],
		order_by: '-id');
		
		foreach($events as $event) {
			$order = Order::get( $event->getOrderId() );
			echo "{$event->getId()}: {$event::getCode()} ({$order?->getNumber()}:{$order?->getId()}})\n";
			$event->handle();
			if(!$event->getErrorMessage()) {
				echo "\tOK\n";
			} else {
				echo "\t{$event->getErrorMessage()}\n";
			}
		}
	}
	
}