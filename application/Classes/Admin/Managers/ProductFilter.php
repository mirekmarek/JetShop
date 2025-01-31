<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use JetShop\Core_Admin_Managers_ProductFilter;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Product Filter',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_ProductFilter extends Core_Admin_Managers_ProductFilter
{

}