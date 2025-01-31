<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
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