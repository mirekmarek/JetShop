<?php
namespace JetShop;

use Jet\Locale;
use JetApplication\EShopConfig;

abstract class Core_DataList {
	
	public static function countries() : array
	{
		/**
		 * @var array $default_countries
		 */
		$default_countries = require EShopConfig::getRootDir() . 'default_countries.php';
		
		$all_locales = Locale::getAllLocalesList();
		$res = [];
		
		foreach($all_locales as $locale_code=>$locale_name) {
			
			$locale = new Locale( $locale_code );
			
			$res[$locale->getRegion()] = $locale->getRegionName();
		}
		asort($res);
		
		$_res = $res;
		
		$res = [];
		foreach($default_countries as $d_country) {
			$res[$d_country] = $_res[$d_country];
			unset($_res[$d_country]);
		}
		
		foreach($_res as $code=>$name) {
			$res[$code] = $name;
		}
		
		return $res;
	}
	
	public static function locales() : array
	{
		/**
		 * @var array $default_locales
		 */
		$default_locales = require EShopConfig::getRootDir() . 'default_locales.php';
		
		
		$all_locales = Locale::getAllLocalesList();
		$res = [];
		
		foreach($all_locales as $locale_code=>$locale_name) {
			
			$locale = new Locale( $locale_code );
			
			$res[$locale->toString()] = $locale->getName();
		}
		asort($res);
		
		$_res = $res;
		
		$res = [];
		foreach($default_locales as $d_locale) {
			$res[$d_locale] = $_res[$d_locale];
			unset($_res[$d_locale]);
		}
		
		foreach($_res as $code=>$name) {
			$res[$code] = $name;
		}
		
		return $res;
		
	}

	
}