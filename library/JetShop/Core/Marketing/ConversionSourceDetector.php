<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Managers_General;
use JetApplication\Marketing_ConversionSourceDetector_Manager;
use JetApplication\Marketing_ConversionSourceDetector_Source;

abstract class Core_Marketing_ConversionSourceDetector
{
	
	public static function getDetector() : ?Marketing_ConversionSourceDetector_Manager
	{
		return Managers_General::MarketingConversionSourceDetector();
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