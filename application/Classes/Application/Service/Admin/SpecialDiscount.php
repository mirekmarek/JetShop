<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;

use Jet\Application_Service_MetaInfo;

#[Application_Service_MetaInfo(
	group: Application_Service_Admin::GROUP,
	is_mandatory: false,
	name: 'Money special discount',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Application_Service_Admin_SpecialDiscount extends Admin_EntityManager_Module
{
}