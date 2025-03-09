<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Manager_MetaInfo;
use JetApplication\Order;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Complaints',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Admin_Managers_Complaint extends Admin_EntityManager_Module
{
	abstract public function showOrderComplaints( Order $order ) : void;
}