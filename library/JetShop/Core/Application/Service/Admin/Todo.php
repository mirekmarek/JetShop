<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_Admin;


#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: false,
	name: 'TODO',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_Todo extends Application_Module
{
	abstract public function renderTool(  string $entity_type, int $entity_id  ) : string;
	
	abstract public function renderHasTodoTag(  string $entity_type, int $entity_id  ) : string;
	
	abstract public function renderDashboard() : string;
}