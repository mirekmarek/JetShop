<?php
/**
 *
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license http://www.php-jet.net/license/license.txt
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use GTClient\Translator;
use Jet\IO_Dir;
use Jet\IO_File;
use Jet\Locale;
use Jet\SysConf_Path;

require __DIR__.'/../application/bootstrap_cli_service.php';

/** @noinspection SpellCheckingInspection */
//$translator = new Translator( 'AIzaSyBM4E1qI3CvrsLK1DH-Cx9sMBYrPcZJjfk' );
$translator = new Translator( 'AIzaSyDiYRxHqxvmIKAqx6jL-zLt5QQNf1kJMXI' );

$locale_en = new Locale('en_US');
$locale_cs = new Locale('cs_CZ');
$locale_sk = new Locale('sk_SK');

$src_locale = $locale_en;
$tg_locale = $locale_sk;


$dictionaries_dir = SysConf_Path::getDictionaries().$tg_locale.'/';

$dictionaries = IO_Dir::getFilesList( $dictionaries_dir );

$translated = [];

foreach($dictionaries as $path=>$name) {
	
	$dictionary = require $path;
	foreach($dictionary as $text=>$translation) {
		if($text && $translation) {
			if(!isset($translated[$text])) {
				$translated[$text] = $translation;
			}
		}
	}
}


foreach($dictionaries as $path=>$name) {
	
	$dictionary = require $path;
	foreach($dictionary as $text=>$translation) {
		if($translation || !$text) {
			continue;
		}
		
		if(!empty($translated[$text])) {
			$translation = $translated[$text];
		} else {
			$translation = $translator->translateText(
				$src_locale->getLanguage(),
				$tg_locale->getLanguage(),
				$text
			);
		}
		
		if($translation) {
			echo $path.':'.PHP_EOL;
			echo $text.PHP_EOL;
			echo $translation.PHP_EOL;
			
			$dictionary[$text] = $translation;
			
			IO_File::writeDataAsPhp( $path, $dictionary );
		}
	}

}

//var_dump($dictionaries_dir);

