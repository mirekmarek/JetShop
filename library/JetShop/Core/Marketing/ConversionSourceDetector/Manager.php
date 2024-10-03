<?php
namespace JetShop;

use JetApplication\Marketing_ConversionSourceDetector_Source;

interface Core_Marketing_ConversionSourceDetector_Manager
{
	/**
	 * @return Marketing_ConversionSourceDetector_Source[]
	 */
	public function getAllSources() : array;
	
	public function performDetection() : void;
	
	public function reset() : void;
	
}