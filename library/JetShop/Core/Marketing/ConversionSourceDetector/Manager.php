<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\Marketing_ConversionSourceDetector_Source;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: false,
	name: 'Conversion source detector',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Marketing_ConversionSourceDetector_Manager extends Application_Module
{
	/**
	 * @return Marketing_ConversionSourceDetector_Source[]
	 */
	abstract public function getAllSources() : array;
	
	abstract public function performDetection() : void;
	
	abstract public function reset() : void;
	
}