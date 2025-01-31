<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use JetShop\Core_EShop_CookieSettings_Manager;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ESHOP,
	is_mandatory: false,
	name: 'Cookie Settings',
	description: '',
	module_name_prefix: 'EShop.'
)]
abstract class EShop_CookieSettings_Manager extends Core_EShop_CookieSettings_Manager {
}