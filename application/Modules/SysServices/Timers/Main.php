<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Timers;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;
use JetApplication\Timer;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$timers = new SysServices_Definition(
			module: $this,
			name: Tr::_('Timers'),
			description: Tr::_('Triggers various timers (e.g. for scheduled price changes, scheduled activations and deactivations, etc.)'),
			service_code: 'perform',
			service: function() {
				$this->runTimers();
			}
		);
		
		return [
			$timers
		];
	}
	
	public function runTimers() : void
	{
		$timers = Timer::toPerform();
		
		foreach($timers as $timer) {
			$timer->perform();
		}
		
	}

}