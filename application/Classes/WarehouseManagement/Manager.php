<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use JetShop\Core_WarehouseManagement_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_GENERAL,
	is_mandatory: false,
	name: 'Warehouse management',
	description: '',
	module_name_prefix: ''
)]
interface WarehouseManagement_Manager extends Core_WarehouseManagement_Manager
{
}