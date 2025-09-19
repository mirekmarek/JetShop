<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_General;
use JetApplication\Marketing_ConversionSourceDetector_Source;

#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: false,
	name: 'Conversion source detector',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_General_ConversionSourceDetector extends Application_Module
{
	/**
	 * @return Marketing_ConversionSourceDetector_Source[]
	 */
	abstract public function getAllSources() : array;
	
	abstract public function performDetection() : void;
	
	abstract public function reset() : void;
	
}