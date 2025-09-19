<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetShop;


use JetApplication\Admin_EntityManager_Module;
use JetApplication\Application_Service_Admin;
use JetApplication\Context;
use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: false,
	name: 'Order Personal Receipt',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Core_Application_Service_Admin_OrderPersonalReceipt extends Admin_EntityManager_Module
{
	abstract public function showDispatches( Context $context ) : string;
	
}