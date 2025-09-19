<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Marketing\PromoArea;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Marketing_PromoArea;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$timeplan = new SysServices_Definition(
			module: $this,
			name: Tr::_('Marketing tools time plan - Promo area'),
			description: Tr::_('Applies a timeline - Promo area'),
			service_code: 'handle_time_plan',
			service: function() {
				Marketing_PromoArea::handleTimePlan();
			}
		);
		
		
		$relations = new SysServices_Definition(
			module: $this,
			name: Tr::_('Promo area - actualize product assoc'),
			description: Tr::_('Updates promo areas assignments to products'),
			service_code: 'actualize_product_assoc',
			service: function() {
				Marketing_PromoArea::actualizeAllRelevantRelations();
			}
		);
		
		
		return [
			$timeplan,
			$relations
		];
	}

}