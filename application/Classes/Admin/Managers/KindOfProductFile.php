<?php
/**
 * @copyright Copyright (c) Miroslav Marek <mirek.marek@web-jet.cz>
 * @license EUPL 1.2  https://eupl.eu/1.2/en/
 * @author Miroslav Marek <mirek.marek@web-jet.cz>
 */
namespace JetApplication;


use JetShop\Core_Admin_Managers_KindOfProductFile;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - products - kind of files',
	description: '',
	module_name_prefix: 'Admin.'
)]
abstract class Admin_Managers_KindOfProductFile extends Core_Admin_Managers_KindOfProductFile
{
}