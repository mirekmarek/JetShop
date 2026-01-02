<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplicationModule\SysServices\Marketing\LandingPage;


use Jet\Application_Module;
use JetApplication\SysServices_Provider_Interface;


class Main extends Application_Module implements SysServices_Provider_Interface
{
	public function getSysServicesDefinitions(): array
	{
		return [];
	}

}