<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use Jet\Application_Service_MetaInfo;
use JetApplication\Application_Service_Admin;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: true,
	name: 'Catalog - property groups',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_PropertyGroup extends Admin_EntityManager_Module
{
	abstract public function renderSelectWidget(
								string $on_select,
								int $selected_property_group_id=0,
								?bool $only_active_filter=null,
								string $name='select_property_group' ) : string;
}