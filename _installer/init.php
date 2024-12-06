<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication\Installer;

use Jet\Config;
use Jet\Locale;
use Jet\SysConf_Jet_Form;
use Jet\SysConf_Jet_UI;
use Jet\SysConf_Path;
use Jet\SysConf_Jet_PackageCreator_CSS;
use Jet\SysConf_Jet_PackageCreator_JavaScript;

Config::setBeTolerant( true );

SysConf_Jet_Form::setDefaultViewsDir( __DIR__.'/views/form/' );
SysConf_Jet_UI::setViewsDir( __DIR__.'/views/ui/' );


require 'Classes/Installer.php';

require_once 'Classes/IntlMock.php';

SysConf_Jet_PackageCreator_CSS::setEnabled( false );
SysConf_Jet_PackageCreator_JavaScript::setEnabled( false );

Installer::setBasePath( SysConf_Path::getBase().'_installer/' );

Installer::setSteps(
	[
		'Welcome',
		'SystemCheck',
		'DirsCheck',
		'SelectDbType',
		'SelectLocales',
		'ConfigURLs',
		'ConfigCurrenciesAndPricelists',
		'ConfigureAvailabilities',
		
		'Install',
		
		//TODO: configure mandatory e-shop modules
		
		'CreateAdministrator',
		'Final',
	]
);


Installer::setAvailableInstallerLocales(
	[
		'en_US', 'cs_CZ',
	]
);

Installer::setAvailableAdminLocales(
	[
		'en_US', 'cs_CZ',
	]
);

Installer::setServicesLocale( new Locale( 'en_US' ) );

Installer::setDefaultCurrencyCode( 'EUR' );
Installer::setDefaultCurrencyCodes([
	'cs_CZ' => 'CZK',
	'pl_PL' => 'PLN',
	'hu_HU' => 'HUF',
	'en_GB' => 'GBP',
	
	'bg_BG' => 'BGN',
	
	'fr_CH' => 'CHF',
	'it_CH' => 'CHF',
	'rm_CH' => 'CHF',
	'de_CH' => 'CHF',
	'de_LI' => 'CHF',
	
	'da_DK' => 'DKK',
	
	'ro_RO' => 'RON',
	
	'sv_SE' => 'SEK',
	
	'nb_NO' => 'NOK',
	'nn_NO' => 'NOK',
	
	'uk_UA' => 'UAH',
	
	'tr_TR' => 'TRY',
	 
	 'is_IS' => 'ISK',
	
]);

Installer::setDefaultVATRates([
	'cs_CZ' => [21, 12, 0],
	'sk_SK' => [20, 10, 5],
	'pl_PL' => [23, 8, 5],
	'de_DE' => [19, 9],
	'de_AT' => [20, 13, 10],
	'hu_HU' => [27, 18, 5],
] );

