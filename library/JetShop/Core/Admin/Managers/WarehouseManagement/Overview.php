<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\Product;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: false,
	name: 'Warehouse Management - Overview',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_WarehouseManagement_Overview extends Admin_EntityManager_Module
{
	abstract public function renderProductStockStatusInfo( Product $product ) : string;
}