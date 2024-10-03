<?php
/**
 *
 * @copyright 
 * @license  
 * @author  
 */
namespace JetApplicationModule\SysServices\MarketingTimePlan\ProductStickers;

use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Marketing_ProductSticker;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;

/**
 *
 */
class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$mtp = new SysServices_Definition(
			module: $this,
			name: Tr::_('Marketing tools time plan - Product stickers'),
			description: Tr::_('Applies a timeline - Product stickers'),
			service_code: 'handle_time_plan',
			service: function() {
				Marketing_ProductSticker::handleTimePlan();
			}
		);
		
		return [
			$mtp
		];
	}

}