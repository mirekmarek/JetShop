<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Marketing\GiftProduct;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Marketing_Gift_Product;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$relations = new SysServices_Definition(
			module: $this,
			name: Tr::_('Gift - Product - actualize product assoc'),
			description: Tr::_('Updates gift assignments to products'),
			service_code: 'actualize_product_assoc',
			service: function() {
				Marketing_Gift_Product::actualizeAllRelevantRelations();
			}
		);
		
		$timeplan = new SysServices_Definition(
			module: $this,
			name: Tr::_('Marketing tools time plan - Gift - Product'),
			description: Tr::_('Applies a timeline - Gift - Product'),
			service_code: 'handle_time_plan',
			service: function() {
				Marketing_Gift_Product::handleTimePlan();
			}
		);
		
		return [
			$relations,
			$timeplan
		];
	}

}