<?php
namespace JetApplication;

use JetShop\Core_Admin_Managers_Property;

#[Manager_MetaInfo(
	group: Manager_MetaInfo::GROUP_ADMIN,
	is_mandatory: true,
	name: 'Catalog - property',
	description: '',
	module_name_prefix: 'Admin.'
)]
interface Admin_Managers_Property extends Core_Admin_Managers_Property
{

}