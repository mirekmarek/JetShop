<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Manager_MetaInfo;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - property groups',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_PropertyGroup extends Admin_EntityManager_Module
{
	abstract public function renderSelectWidget(
								string $on_select,
								int $selected_property_group_id=0,
								?bool $only_active_filter=null,
								string $name='select_property_group' ) : string;
}