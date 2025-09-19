<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Application_Service_Admin;
use JetApplication\EShopEntity_HasTimer_Interface;
use Jet\Application_Service_MetaInfo;


#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: false,
	name: 'Timer',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_Timer extends Application_Module
{
	abstract public function renderIntegration() : string;
	
	abstract public function renderEntityEdit( EShopEntity_HasTimer_Interface $entity, bool $editable ) : string;

}