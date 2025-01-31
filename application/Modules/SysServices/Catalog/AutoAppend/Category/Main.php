<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Catalog\AutoAppend\Category;


use Jet\Application_Module;
use Jet\Tr;
use JetApplication\Category;
use JetApplication\SysServices_Definition;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		$auto_append = new SysServices_Definition(
			module: $this,
			name: Tr::_('Catalog - Actualize AutoAppend - Category'),
			description: Tr::_('Provides the function of automatically associating products to categories within the product catalogue.'),
			service_code: 'actualize_fulltext',
			service: function() {
				Category::actualizeAllAutoAppendCategories();
			}
		);
		
		return [
			$auto_append
		];
	}
	
}