<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;

use Jet\Application_Module;
use Jet\Locale;
use JetApplication\EShop;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: false,
	name: 'Translator',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Translator_Manager extends Application_Module
{
	
	abstract public function translateShortText( EShop|Locale $from, EShop|Locale $to, string $text ): string;
	abstract public function translateLongText( EShop|Locale $from, EShop|Locale $to, string $text ): string;
	
}