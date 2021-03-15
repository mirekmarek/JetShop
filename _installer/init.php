<?php
/**
 *
 * @copyright Copyright (c) 2011-2021 Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop\Installer;

use Jet\Config;
use Jet\Form;
use Jet\SysConf_Path;
use Jet\UI;

Config::setBeTolerant( true );

Form::setDefaultViewsDir( __DIR__.'/views/Form/' );
UI::setViewsDir( __DIR__.'/views/UI/' );

require 'Classes/Installer.php';

Installer::setBasePath( SysConf_Path::getBase().'_installer/' );

Installer::setSteps(
	[
		'Welcome',
		'SystemCheck',
		'DirsCheck',
		'SelectDbType',
		'CreateDB',
		'SelectLocales',
		'CreateSite',
		'Mailing',
		'InstallModules',
		'CreateAdministrator',
		'ConfigureStudio',
		'Final',
	]
);


Installer::setAvailableLocales(
	[
		'cs_CZ',
	]
);
