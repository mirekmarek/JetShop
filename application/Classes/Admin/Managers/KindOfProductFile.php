<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_KindOfProductFile;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - products - kind of files',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_KindOfProductFile extends Core_Admin_Managers_KindOfProductFile
{
}