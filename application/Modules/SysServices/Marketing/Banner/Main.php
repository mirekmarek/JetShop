<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Marketing\Banner;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Marketing_Banner;
use JetApplication\Marketing_CategoryBanner;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$timeplan = new SysServices_Definition(
			module: $this,
			name: Tr::_('Marketing tools time plan - Banner'),
			description: Tr::_('Applies a timeline - Banner'),
			service_code: 'handle_time_plan',
			service: function() {
				Marketing_Banner::handleTimePlan();
				Marketing_CategoryBanner::handleTimePlan();
			}
		);
		
		return [
			$timeplan
		];
	}

}