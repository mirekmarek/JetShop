<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Application_Service_General;
use JetApplication\Application_Service_General_ConversionSourceDetector;
use JetApplication\Marketing_ConversionSourceDetector_Source;

abstract class Core_Marketing_ConversionSourceDetector
{
	
	public static function getDetector() : ?Application_Service_General_ConversionSourceDetector
	{
		return Application_Service_General::MarketingConversionSourceDetector();
	}
	
	public function getScope() : array
	{
		$sources = static::getAllSources();
		$result = [];
		
		foreach($sources as $s) {
			$result[$s->getName()] = $s->getName();
		}
		
		return $result;
		
	}
	
	/**
	 * @return Marketing_ConversionSourceDetector_Source[]
	 */
	public static function getAllSources() : array
	{
		return static::getDetector()?->getAllSources()??[];
	}
	
	public static function performDetection() : void
	{
		static::getDetector()?->performDetection();
	}
	
	/**
	 * @return Marketing_ConversionSourceDetector_Source[]
	 */
	public static function getDetectedSources() : array
	{
		$sources = static::getAllSources();
		
		$result = [];
		
		foreach($sources as $s) {
			if($s->isDetected()) {
				$result[] = $s;
			}
		}
		
		return $result;
	}
	
	
}