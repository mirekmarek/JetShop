<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Marketing\GiftShoppingCart;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Marketing_Gift_ShoppingCart;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$timeplan = new SysServices_Definition(
			module: $this,
			name: Tr::_('Marketing tools time plan - Gift - Shopping cart'),
			description: Tr::_('Applies a timeline - Gift - Shopping cart'),
			service_code: 'handle_time_plan',
			service: function() {
				Marketing_Gift_ShoppingCart::handleTimePlan();
			}
		);
		
		
		$relations = new SysServices_Definition(
			module: $this,
			name: Tr::_('Gift - shopping cart - actualize product assoc'),
			description: Tr::_('Updates gift - shopping cart assignments to products'),
			service_code: 'actualize_product_assoc',
			service: function() {
				Marketing_Gift_ShoppingCart::actualizeAllRelevantRelations();
			}
		);
		
		
		return [
			$timeplan,
			$relations
		];
	}

}