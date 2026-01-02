<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Marketing\DiscountCodes;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Discounts_Code;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$timeplan = new SysServices_Definition(
			module: $this,
			name: Tr::_('Marketing tools time plan - Discount Codes'),
			description: Tr::_('Applies a timeline - Discount Codes'),
			service_code: 'handle_time_plan',
			service: function() {
				Discounts_Code::handleTimePlan();
			}
		);
		
		$relations = new SysServices_Definition(
			module: $this,
			name: Tr::_('Discount Codes - actualize product assoc'),
			description: Tr::_('Updates Discount Codes assignments to products'),
			service_code: 'actualize_product_assoc',
			service: function() {
				Discounts_Code::actualizeAllRelevantRelations();
			}
		);
		
		
		return [
			$timeplan,
			$relations
		];
	}

}