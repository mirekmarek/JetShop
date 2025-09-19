<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_General;
use JetApplication\EShopEntity_HasNumberSeries_Interface;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_General::GROUP,
	is_mandatory: true,
	name: 'Number series',
	description: '',
	module_name_prefix: ''
)]
abstract class Core_Application_Service_General_NumberSeries extends Application_Module {
	
	abstract public function generateNumber( EShopEntity_HasNumberSeries_Interface $entity ) : string;
	
}