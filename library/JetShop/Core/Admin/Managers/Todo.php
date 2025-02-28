<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use Jet\Application_Module;
use JetApplication\Manager_MetaInfo;


#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'TODO',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_Todo extends Application_Module
{
	abstract public function renderTool(  string $entity_type, int $entity_id  ) : string;
	
	abstract public function renderDashboard() : string;
}